<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    StoreCredit
 * @version    1.1.7
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\StoreCredit\Block\Sales\Order;

use Magento\Framework\DataObject\Factory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Aheadworks\StoreCredit\Block\Sales\Order\Total
 */
class Total extends Template
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Context $context
     * @param Factory $factory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $factory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->factory = $factory;
    }

    /**
     * Retrieve sales order model
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock) {
            return $parentBlock->getOrder();
        }
        return null;
    }

    /**
     * Retrieve totals source object
     *
     * @return \Magento\Sales\Model\Order|\Magento\Sales\Model\Order\Invoice
     */
    public function getSource()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock) {
            return $parentBlock->getSource();
        }
        return null;
    }

    /**
     * Initialize Store Credit order total
     *
     * @return \Aheadworks\StoreCredit\Block\Sales\Order\Total
     */
    public function initTotals()
    {
        $order = $this->getOrder();
        if ($order) {
            $source = $this->getSource();
            if ($source) {
                if ($order->getAwUseStoreCredit()) {
                    $this->getParentBlock()->addTotal(
                        $this->factory->create(
                            [
                                'code'   => 'aw_store_credit',
                                'strong' => false,
                                'label'  => __('Store Credit'),
                                'value'  => $source->getAwStoreCreditAmount(),
                            ]
                        )
                    );
                }
                if ($source->getAwStoreCreditRefunded()) {
                    $this->getParentBlock()->addTotal(
                        $this->factory->create(
                            [
                                'code'   => 'aw_store_credit_refunded',
                                'strong' => false,
                                'label'  => __('Returned to Store Credit'),
                                'value'  => $source->getAwStoreCreditRefunded(),
                            ]
                        ),
                        'last'
                    );
                }
                if ($source->getAwStoreCreditReimbursed()) {
                    $this->getParentBlock()->addTotal(
                        $this->factory->create(
                            [
                                'code'   => 'aw_store_credit_reimbursed',
                                'strong' => false,
                                'label'  => __('Reimbursed spent Store Credit'),
                                'value'  => $source->getAwStoreCreditReimbursed(),
                            ]
                        ),
                        'last'
                    );
                }
            }
        }
        return $this;
    }
}
