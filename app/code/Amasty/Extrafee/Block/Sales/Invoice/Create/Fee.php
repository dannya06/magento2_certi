<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Block\Sales\Invoice\Create;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Amasty\Extrafee\Block\Sales\Fees;
use Amasty\Extrafee\Model\ConfigProvider;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\CollectionFactory as FeeOrderCollectionFactory;
use Magento\Framework\View\Element\Template;

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

        $feeOrderCollection = $this->feeOrderCollectionFactory->create()
            ->addFieldToFilter(ExtrafeeOrderInterface::ORDER_ID, $parent->getOrder()->getId());

        /** @var \Amasty\Extrafee\Model\ExtrafeeOrder $feeOrder */
        foreach ($feeOrderCollection->getItems() as $feeOrder) {
            if ($feeOrder->getBaseTotalAmountInvoiced() == 0) {
                $this->getFees($parent, $feeOrder);
            }
        }

        return $this;
    }
}
