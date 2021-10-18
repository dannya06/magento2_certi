<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icube\Quote\Model;

/**
 * Quote model
 * @since 100.0.2
 */
class Quote
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    ) {
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\Quote\Model\ResourceModel\Quote::class);
    }

    /**
     * Trigger collect totals after loading, if required
     *
     * @return $this
     */
    protected function around_afterLoad()
    {
        // collect totals and save me, if required
        if (1 == $this->getTriggerRecollect()) {
            // this plugin until magento solv 
            $this->setTriggerRecollect(0)->save();
        }
    }
}
