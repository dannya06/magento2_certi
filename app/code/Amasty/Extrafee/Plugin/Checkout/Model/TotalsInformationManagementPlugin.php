<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Plugin\Checkout\Model;

use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Checkout\Model\TotalsInformationManagement;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class TotalsInformationManagementPlugin
{
    const PAYMENT_METHOD = 'payment_method';

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        DataPersistorInterface $dataPersistor
    ) {
        $this->cartRepository = $cartRepository;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @param TotalsInformationManagement $subject
     * @param int $cartId
     * @param TotalsInformationInterface $addressInformation
     *
     * @return array
     */
    public function beforeCalculate(
        TotalsInformationManagement $subject,
        $cartId,
        TotalsInformationInterface $addressInformation
    ) {
        $paymentMethod = null;
        $attributes = $addressInformation->getAddress()->getExtensionAttributes();

        if ($attributes && is_object($attributes)) {
            if (method_exists($attributes, 'getAdvancedConditions')) {
                $advancedConditions = $attributes->getAdvancedConditions();
                $paymentMethod = $advancedConditions
                    ? $advancedConditions->getPaymentMethod()
                    : $this->dataPersistor->get(self::PAYMENT_METHOD);
            }

            $this->dataPersistor->set(self::PAYMENT_METHOD, $paymentMethod);
        } else {
            $paymentMethod = $this->dataPersistor->get(self::PAYMENT_METHOD);
        }

        $quote = $this->cartRepository->get($cartId);
        $quote->setPaymentMethod($paymentMethod);

        return [$cartId, $addressInformation];
    }
}
