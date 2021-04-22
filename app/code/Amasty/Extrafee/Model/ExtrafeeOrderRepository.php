<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;
use Amasty\Extrafee\Api\ExtrafeeOrderRepositoryInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeOrder as FeeOrderResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ExtrafeeOrderRepository implements ExtrafeeOrderRepositoryInterface
{
    /**
     * @var FeeOrderResource
     */
    private $feeOrderResource;

    /**
     * @var ExtrafeeOrderFactory
     */
    private $feeOrderFactory;

    /**
     * Model data storage
     *
     * @var array
     */
    private $orderFees;

    public function __construct(FeeOrderResource $feeOrderResource, ExtrafeeOrderFactory $feeOrderFactory)
    {
        $this->feeOrderResource = $feeOrderResource;
        $this->feeOrderFactory = $feeOrderFactory;
    }

    /**
     * @param ExtrafeeOrderInterface $orderFee
     * @return ExtrafeeOrderInterface
     * @throws CouldNotSaveException
     */
    public function save(ExtrafeeOrderInterface $orderFee): ExtrafeeOrderInterface
    {
        try {
            if ($orderFee->getEntityId()) {
                $orderFee = $this->getById((int)$orderFee->getEntityId())->addData($orderFee->getData());
            }
            $this->feeOrderResource->save($orderFee);
            unset($this->orderFees[$orderFee->getEntityId()]);
        } catch (\Exception $e) {
            if ($orderFee->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save order fee with ID %1. Error: %2',
                        [$orderFee->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new order fee. Error: %1', $e->getMessage()));
        }

        return $orderFee;
    }

    /**
     * @param int $entityId
     * @return ExtrafeeOrderInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): ExtrafeeOrderInterface
    {
        if (!isset($this->orderFees[$entityId])) {
            $orderFee = $this->feeOrderFactory->create();
            $this->feeOrderResource->load($orderFee, $entityId);
            if (!$orderFee->getEntityId()) {
                throw new NoSuchEntityException(__('Order fee with specified ID "%1" not found.', $entityId));
            }
            $this->orderFees[$entityId] = $orderFee;
        }

        return $this->orderFees[$entityId];
    }

    /**
     * @param ExtrafeeOrderInterface $orderFee
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ExtrafeeOrderInterface $orderFee): bool
    {
        try {
            $this->feeOrderResource->delete($orderFee);
            unset($this->orderFees[$orderFee->getEntityId()]);
        } catch (\Exception $e) {
            if ($orderFee->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove order fee with ID %1. Error: %2',
                        [$orderFee->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove order fee. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * Get list of fees' labels for orders. [order_id => labels]
     *
     * @param array $orderIds
     * @return array [order_id => labelsStringConcatenated]
     */
    public function getLabelsForOrders(array $orderIds): array
    {
        return $this->feeOrderResource->getLabelsForOrders($orderIds);
    }
}
