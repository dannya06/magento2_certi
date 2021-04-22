<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Block\Sales\CreditMemo\View;

use Amasty\Extrafee\Api\Data\ExtrafeeCreditmemoInterface;
use Amasty\Extrafee\Block\Sales\Fees;
use Amasty\Extrafee\Model\ConfigProvider;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeCreditmemo\CollectionFactory as FeeCreditmemoCollectionFactory;
use Magento\Framework\View\Element\Template;

class Fee extends Fees
{
    /**
     * @var FeeCreditmemoCollectionFactory
     */
    private $feeCreditmemoCollectionFactory;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        FeeCreditmemoCollectionFactory $feeCreditmemoCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $configProvider, $data);
        $this->feeCreditmemoCollectionFactory = $feeCreditmemoCollectionFactory;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();

        $feeCreditmemoCollection = $this->feeCreditmemoCollectionFactory->create()
            ->addFieldToFilter(ExtrafeeCreditmemoInterface::CREDITMEMO_ID, $parent->getSource()->getId());

        foreach ($feeCreditmemoCollection->getItems() as $feeCreditmemo) {
            $this->getFees($parent, $feeCreditmemo);
        }

        return $this;
    }
}
