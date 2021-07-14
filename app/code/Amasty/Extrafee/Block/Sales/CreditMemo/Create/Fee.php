<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Block\Sales\CreditMemo\Create;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Amasty\Extrafee\Block\Sales\Fees;
use Amasty\Extrafee\Model\ConfigProvider;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder\CollectionFactory as FeeOrderCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template;

class Fee extends Fees
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var FeeOrderCollectionFactory
     */
    private $feeOrderCollectionFactory;

    /**
     * @var int
     */
    private $orderId;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        RequestInterface $request,
        FeeOrderCollectionFactory $feeOrderCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $configProvider, $data);
        $this->request = $request;
        $this->feeOrderCollectionFactory = $feeOrderCollectionFactory;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $total = new \Magento\Framework\DataObject(
            [
                'code' => 'amasty_extafee',
                'block_name' => $this->getNameInLayout()
            ]
        );
        $parent->addTotal($total);

        $this->orderId = $parent->getOrder()->getId();

        return $this;
    }

    /**
     * @return ExtrafeeOrderInterface[]
     */
    public function getCreditMemoFees()
    {
        $creditmemoPost = $this->request->getParam('creditmemo');
        $feeOrderCollection = $this->feeOrderCollectionFactory->create()
            ->getFeeOrderCollectionByOrderId($this->orderId);

        /** @var \Amasty\Extrafee\Model\ExtrafeeOrder $feeOrder */
        foreach ($feeOrderCollection->getItems() as $feeOrder) {
            if (isset($creditmemoPost['extra_fee_' . $feeOrder->getFeeId() . '_' . $feeOrder->getOptionId()])) {
                $feeOrder->setChosenTotal(
                    $creditmemoPost['extra_fee_' . $feeOrder->getFeeId() . '_' . $feeOrder->getOptionId()]
                );
            }
        }

        return $feeOrderCollection->getItems();
    }
}
