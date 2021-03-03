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
namespace Aheadworks\StoreCredit\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Aheadworks\StoreCredit\Model\Config;

/**
 * Class Aheadworks\StoreCredit\Model\Total\Creditmemo\StoreCredit
 */
class StoreCredit extends AbstractTotal
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     *  {@inheritDoc}
     */
    public function collect(Creditmemo $creditMemo)
    {
        $creditMemo->setAwUseStoreCredit(false);
        $creditMemo->setAwStoreCreditAmount(0);
        $creditMemo->setBaseAwStoreCreditAmount(0);
        $creditMemo->setBaseAwStoreCreditRefunded(0);
        $creditMemo->setAwStoreCreditRefunded(0);
        $creditMemo->setBaseAwStoreCreditRefunded(0);
        $creditMemo->setAwStoreCreditRefunded(0);
        $creditMemo->setBaseAwStoreCreditReimbursed(0);
        $creditMemo->setAwStoreCreditReimbursed(0);

        $creditMemo->setBaseAwStoreCreditRefundValue(0);
        $creditMemo->setAwStoreCreditRefundValue(0);

        $order = $creditMemo->getOrder();

        if ($order->getBaseAwStoreCreditAmount() && $order->getBaseAwStoreCreditInvoiced() != 0) {
            $awscLeft = $order->getBaseAwStoreCreditInvoiced() + $order->getBaseAwStoreCreditReimbursed();
            $baseUsed = $creditMemo->getBaseGrandTotal();
            $used = $creditMemo->getGrandTotal();
            $baseGrandTotal = 0;
            $grandTotal = 0;

            if (!$this->config->isApplyingStoreCreditOnTax($order->getStore()->getWebsiteId())) {
                $baseUsed -= $creditMemo->getBaseTaxAmount();
                $used -= $creditMemo->getTaxAmount();
                $baseGrandTotal += $creditMemo->getBaseTaxAmount();
                $grandTotal += $creditMemo->getTaxAmount();
            }
            if (!$this->config->isApplyingStoreCreditOnShipping($order->getStore()->getWebsiteId())) {
                $baseUsed -= $creditMemo->getBaseShippingAmount();
                $used -= $creditMemo->getShippingAmount();
                $baseGrandTotal += $creditMemo->getBaseShippingAmount();
                $grandTotal += $creditMemo->getShippingAmount();
            }

            if (abs($awscLeft) >= $baseUsed) {
                $creditMemo->setBaseGrandTotal($baseGrandTotal);
                $creditMemo->setGrandTotal($grandTotal);
                if ($creditMemo->getGrandTotal() == 0) {
                    $creditMemo->setAllowZeroGrandTotal(true);
                }
            } else {
                $baseUsed = abs($order->getBaseAwStoreCreditInvoiced()) - abs($order->getBaseAwStoreCreditReimbursed());
                $used = abs($order->getAwStoreCreditInvoiced()) - abs($order->getAwStoreCreditReimbursed());

                $creditMemo->setBaseGrandTotal($creditMemo->getBaseGrandTotal() - $baseUsed);
                $creditMemo->setGrandTotal($creditMemo->getGrandTotal() - $used);
            }

            if ($baseUsed > 0) {
                $creditMemo->setAwUseStoreCredit($order->getAwUseStoreCredit());
                $creditMemo->setBaseAwStoreCreditAmount(-$baseUsed);
                $creditMemo->setAwStoreCreditAmount(-$used);
            }
        }

        $creditMemo->setBaseAwStoreCreditRefundValue($creditMemo->getBaseGrandTotal());
        $creditMemo->setAwStoreCreditRefundValue($creditMemo->getGrandTotal());

        return $this;
    }
}
