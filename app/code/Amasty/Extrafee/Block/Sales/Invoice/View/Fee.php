<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Block\Sales\Invoice\View;

use Amasty\Extrafee\Api\Data\ExtrafeeInvoiceInterface;
use Amasty\Extrafee\Block\Sales\Fees;
use Amasty\Extrafee\Model\ConfigProvider;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeInvoice\CollectionFactory as FeeInvoiceCollectionFactory;
use Magento\Framework\View\Element\Template;

class Fee extends Fees
{
    /**
     * @var FeeInvoiceCollectionFactory
     */
    private $feeInvoiceCollectionFactory;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        FeeInvoiceCollectionFactory $feeInvoiceCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $configProvider, $data);
        $this->feeInvoiceCollectionFactory = $feeInvoiceCollectionFactory;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();

        $feeInvoiceCollection = $this->feeInvoiceCollectionFactory->create()
            ->addFieldToFilter(ExtrafeeInvoiceInterface::INVOICE_ID, $parent->getSource()->getId());

        foreach ($feeInvoiceCollection->getItems() as $feeInvoice) {
            $this->getFees($parent, $feeInvoice);
        }

        return $this;
    }
}
