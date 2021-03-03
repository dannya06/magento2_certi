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
namespace Aheadworks\StoreCredit\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Aheadworks\StoreCredit\Observer\IncreaseOrderStoreCreditInvoicedAmount
 */
class IncreaseOrderStoreCreditInvoicedAmount implements ObserverInterface
{
    /**
     * Increase order aw_store_credit_invoiced attribute based on created invoice
     * used for event: sales_order_invoice_register
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($invoice->getBaseAwStoreCreditAmount()) {
            $order->setBaseAwStoreCreditInvoiced(
                $order->getBaseAwStoreCreditInvoiced() + $invoice->getBaseAwStoreCreditAmount()
            );
            $order->setAwStoreCreditInvoiced(
                $order->getAwStoreCreditInvoiced() + $invoice->getAwStoreCreditAmount()
            );
        }
        return $this;
    }
}
