<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Promo
 */


namespace Amasty\Promo\Plugin;

class SalesRule
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->coreRegistry = $registry;
    }

    public function afterSave(\Magento\SalesRule\Model\Rule $subject, $result)
    {
        $this->coreRegistry->register('ampromo_current_salesrule', $subject, true);

        return $result;
    }
}
