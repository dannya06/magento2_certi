<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

namespace Amasty\Orderattr\Plugin\Quote;

use Magento\Framework\Json\DecoderInterface;

class PaymentMethodManagement
{
    /**
     * @var \Amasty\Orderattr\Model\OrderAttributesManagement
     */
    protected $_orderAttributesManager;
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;
    /**
     * @var DecoderInterface
     */
    private $jsonDecoder;

    public function __construct(
        \Amasty\Orderattr\Model\OrderAttributesManagement $orderAttributesManager,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        DecoderInterface $jsonDecoder
    ) {

        $this->_orderAttributesManager = $orderAttributesManager;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->jsonDecoder = $jsonDecoder;
    }

    public function beforeSet($subject, $cartId, \Magento\Quote\Api\Data\PaymentInterface $method)
    {
        $data = $method->getData();
        if (isset($data['additional_data']['custom_attributes']) && $data['additional_data']['custom_attributes']) {
            $orderAttributes = $this->jsonDecoder->decode($data['additional_data']['custom_attributes']);
            $orderAttributes = $this->filterOrderAttributesFromCheckout($orderAttributes);
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            $quoteId = $quoteIdMask->getQuoteId();
            if (!$quoteId) {
                $quoteId = $cartId;
            }
            $this->_orderAttributesManager->saveAttributesFromQuote($quoteId, $orderAttributes);
        }

        return [$cartId, $method];
    }

    protected function filterOrderAttributesFromCheckout($orderAttributes)
    {
        $orderAttributesList = [];
        foreach ($orderAttributes as $attributeCode => $attributeValue) {
            if (strpos($attributeCode, 'amorderattr_') !== false) {
                $orderAttributesList[str_replace('amorderattr_', '', $attributeCode)] = $attributeValue;
            }
        }
        return $orderAttributesList;
    }

}
