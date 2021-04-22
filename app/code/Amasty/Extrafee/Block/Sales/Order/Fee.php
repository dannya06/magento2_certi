<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Block\Sales\Order;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Amasty\Extrafee\Block\Sales\Fees;
use Amasty\Extrafee\Model\ConfigProvider;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\CollectionFactory as FeeOrderCollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\Data\OrderInterface;

class Fee extends Fees
{
    /**
     * @var FeeOrderCollectionFactory
     */
    private $feeOrderCollectionFactory;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        FeeOrderCollectionFactory $feeOrderCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $configProvider, $data);
        $this->feeOrderCollectionFactory = $feeOrderCollectionFactory;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();

        if (!$parent || !method_exists($parent, 'getOrder')) {
            return $this;
        }
        $order = $parent->getOrder();

        if (!($order instanceof OrderInterface)) {
            return $this;
        }

        $feeOrderCollection = $this->feeOrderCollectionFactory->create()
            ->addFieldToFilter(ExtrafeeOrderInterface::ORDER_ID, $order->getId());

        foreach ($feeOrderCollection->getItems() as $feeOrder) {
            $this->getFees($parent, $feeOrder);
        }

        return $this;
    }
}
