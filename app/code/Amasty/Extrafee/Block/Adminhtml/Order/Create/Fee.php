<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Block\Adminhtml\Order\Create;

use Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate;

class Fee extends AbstractCreate
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_amasty_extrafee');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Additional Fees');
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-amasty-extrafee';
    }
}
