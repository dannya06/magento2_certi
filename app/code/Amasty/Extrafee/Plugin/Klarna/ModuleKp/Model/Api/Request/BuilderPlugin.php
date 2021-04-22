<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Plugin\Klarna\ModuleKp\Model\Api\Request;

use Klarna\Kp\Model\Api\Request\Builder;
use Magento\Checkout\Model\Session as CheckoutSession;

class BuilderPlugin
{
    /** @var CheckoutSession */
    private $checkoutSession;

    public function __construct(
        CheckoutSession $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Set extra fee in Klarna totals
     *
     * @param Builder $subject
     * @param $data
     * @return array
     */
    public function beforeAddOrderlines(Builder $subject, $data)
    {
        $totals = $this->checkoutSession->getQuote()->getTotals();

        if (isset($totals['amasty_extrafee']) && $totals['amasty_extrafee']->getValue() > 0) {
            $amount = round($totals['amasty_extrafee']->getValue() * 100);

            $paymentFeeData = [
                [
                    'type' => 'surcharge',
                    'unit_price' => $amount,
                    'quantity' => 1,
                    'name' => $totals['amasty_extrafee']->getTitle(),
                    'total_amount' => $amount
                ]
            ];

            $data = array_merge($paymentFeeData, $data);
        }

        return [$data];
    }
}
