<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Block\Adminhtml\View;

use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Filter\CustomerGroup as CustomerGroupFilter;

/**
 * Class CustomerGroup
 *
 * @package Aheadworks\AdvancedReports\Block\Adminhtml\View
 */
class CustomerGroup extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_AdvancedReports::view/customer-group.phtml';

    /**
     * @var CustomerGroupFilter
     */
    private $customerGroupFilter;

    /**
     * @param Context $context
     * @param CustomerGroupFilter $customerGroupFilter
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerGroupFilter $customerGroupFilter,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerGroupFilter = $customerGroupFilter;
    }

    /**
     * Retrieve customer group items
     *
     * @return []
     */
    public function getItems()
    {
        return $this->customerGroupFilter->getItems();
    }

    /**
     * Retrieve current item key
     *
     * @return string
     */
    public function getCurrentItemKey()
    {
        return $this->customerGroupFilter->getCurrentItemKey();
    }

    /**
     * Retrieve current item title
     *
     * @return string
     */
    public function getCurrentItemTitle()
    {
        $items = $this->getItems();
        $key = $this->getCurrentItemKey();
        if (!array_key_exists($key, $items)) {
            return '';
        }
        return $items[$key]['title'];
    }
}
