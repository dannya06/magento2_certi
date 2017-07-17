<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */


namespace Amasty\Promo\Plugin;

use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Metadata\ValueProvider as SalesRuleValueProvider;

class ValueProvider
{

    /**
     * @var \Amasty\Promo\Model\RuleFactory
     */
    private $ruleFactory;

    public function __construct(
        \Amasty\Promo\Model\RuleFactory $ruleFactory
    ) {
        $this->ruleFactory = $ruleFactory;
    }

    public function aroundGetMetadataValues(
        SalesRuleValueProvider $subject,
        \Closure $proceed,
        Rule $rule
    ) {
        $result = $proceed($rule);

        $actions = &$result['actions']['children']['simple_action']['arguments']['data']['config']['options'];

        $actions[] = [
            'label' => __('Auto add promo items with products'),
            'value' => 'ampromo_items'
        ];
        $actions[] = [
            'label' => __('Auto add promo items for the whole cart'),
            'value' => 'ampromo_cart'
        ];
        $actions[] = [
            'label' => __('Auto add the same product'),
            'value' => 'ampromo_product'
        ];
        $actions[] = [
            'label' => __('Auto add promo items for every $X spent'),
            'value' => 'ampromo_spent'
        ];

        /** @var \Amasty\Promo\Model\Rule $ampromoRule */
        $ampromoRule = $this->ruleFactory->create();
        $ampromoRule->load($rule->getId(), 'salesrule_id');

        $result['actions']['children']['ampromorule[sku]']['arguments']['data']['config']['value']
            = $ampromoRule->getData('sku');

        $result['actions']['children']['ampromorule[type]']['arguments']['data']['config']['value']
            = $ampromoRule->getData('type');

        if ($ampromoRule->getData('top_banner_image')) {
            $result['ampromorule_top_banner']['children']['ampromorule_top_banner_image']['arguments']['data']['config']['value']
                = @unserialize($ampromoRule->getData('top_banner_image'));
        }

        $result['ampromorule_top_banner']['children']['ampromorule_top_banner_alt']['arguments']['data']['config']['value']
            = $ampromoRule->getData('top_banner_alt');

        $result['ampromorule_top_banner']['children']['ampromorule_top_banner_on_hover_text']['arguments']['data']['config']['value']
            = $ampromoRule->getData('top_banner_on_hover_text');

        $result['ampromorule_top_banner']['children']['ampromorule_top_banner_link']['arguments']['data']['config']['value']
            = $ampromoRule->getData('top_banner_link');

        $result['ampromorule_top_banner']['children']['ampromorule_top_banner_show_gift_images']['arguments']['data']['config']['value']
            = $ampromoRule->getData('top_banner_show_gift_images');

        $result['ampromorule_top_banner']['children']['ampromorule_top_banner_description']['arguments']['data']['config']['value']
            = $ampromoRule->getData('top_banner_description');

        if ($ampromoRule->getData('after_product_banner_image')) {
            $result['ampromorule_after_product_banner']['children']['ampromorule_after_product_banner_image']['arguments']['data']['config']['value']
                = @unserialize($ampromoRule->getData('after_product_banner_image'));
        }

        $result['ampromorule_after_product_banner']['children']['ampromorule_after_product_banner_alt']['arguments']['data']['config']['value']
            = $ampromoRule->getData('after_product_banner_alt');

        $result['ampromorule_after_product_banner']['children']['ampromorule_after_product_banner_on_hover_text']['arguments']['data']['config']['value']
            = $ampromoRule->getData('after_product_banner_on_hover_text');

        $result['ampromorule_after_product_banner']['children']['ampromorule_after_product_banner_link']['arguments']['data']['config']['value']
            = $ampromoRule->getData('after_product_banner_link');

        $result['ampromorule_after_product_banner']['children']['ampromorule_after_product_banner_show_gift_images']['arguments']['data']['config']['value']
            = $ampromoRule->getData('after_product_banner_show_gift_images');

        $result['ampromorule_after_product_banner']['children']['ampromorule_after_product_banner_description']['arguments']['data']['config']['value']
            = $ampromoRule->getData('after_product_banner_description');

        if ($ampromoRule->getData('label_image')) {
            $result['ampromorule_product_label']['children']['ampromorule_label_image']['arguments']['data']['config']['value']
                = @unserialize($ampromoRule->getData('label_image'));
        }

        $result['ampromorule_product_label']['children']['ampromorule_label_image_alt']['arguments']['data']['config']['value']
            = $ampromoRule->getData('label_image_alt');

        return $result;
    }
}
