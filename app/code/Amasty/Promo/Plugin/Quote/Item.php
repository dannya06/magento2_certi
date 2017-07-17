<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */


namespace Amasty\Promo\Plugin\Quote;

use Magento\Quote\Model\Quote\Item\AbstractItem;

class Item
{
    /**
     * @var \Amasty\Promo\Helper\Item
     */
    protected $promoItemHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Amasty\Promo\Helper\Item $promoItemHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->promoItemHelper = $promoItemHelper;
        $this->scopeConfig     = $scopeConfig;
    }

    public function beforeSetData(AbstractItem $subject, $key, $value = null)
    {
        if (!is_string($key)) {
            return [$key, $value];
        }

        $fields = [
            'price',
            'base_price',
            'custom_price',
            'original_custom_price',
            'price_incl_tax',
            'base_price_incl_tax'
        ];

        if (in_array($key, $fields)) {
            if ($this->promoItemHelper->isPromoItem($subject)) {
                return [$key, 0];
            }
        }

        return [$key, $value];
    }

    public function aroundRepresentProduct(
        AbstractItem $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        if ($proceed($product)) {
            $productRuleId = $product->getData('ampromo_rule_id');
            $itemRuleId    = $this->promoItemHelper->getRuleId($subject);

            return $productRuleId === $itemRuleId;
        } else {
            return false;
        }
    }

    public function aroundGetMessage(
        AbstractItem $subject,
        \Closure $proceed,
        $string = true
    ) {
        $result = $proceed($string);

        if ($this->promoItemHelper->isPromoItem($subject)) {

            $customMessage = $this->scopeConfig->getValue(
                'ampromo/messages/cart_message',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if ($customMessage) {
                if ($string) {
                    $result .= "\n" . $customMessage;
                } else {
                    $result [] = $customMessage;
                }
            }
        }

        return $result;
    }
}
