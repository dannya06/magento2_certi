<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\ExtrafeeCreditmemoInterface;
use Amasty\Extrafee\Api\ExtrafeeCreditmemoRepositoryInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeCreditmemo as FeeCreditmemoResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ExtrafeeCreditmemoRepository implements ExtrafeeCreditmemoRepositoryInterface
{
    /**
     * @var FeeCreditmemoResource
     */
    private $feeCreditmemoResource;

    /**
     * @var ExtrafeeCreditmemoFactory
     */
    private $feeCreditmemoFactory;

    /**
     * Model data storage
     *
     * @var array
     */
    private $creditmemoFees;

    public function __construct(
        FeeCreditmemoResource $feeCreditmemoResource,
        ExtrafeeCreditmemoFactory $feeCreditmemoFactory
    ) {
        $this->feeCreditmemoResource = $feeCreditmemoResource;
        $this->feeCreditmemoFactory = $feeCreditmemoFactory;
    }

    /**
     * @param ExtrafeeCreditmemoInterface $creditmemoFee
     * @return ExtrafeeCreditmemoInterface
     * @throws CouldNotSaveException
     */
    public function save(ExtrafeeCreditmemoInterface $creditmemoFee): ExtrafeeCreditmemoInterface
    {
        try {
            if ($creditmemoFee->getEntityId()) {
                $creditmemoFee = $this->getById((int)$creditmemoFee->getEntityId())->addData($creditmemoFee->getData());
            }
            $this->feeCreditmemoResource->save($creditmemoFee);
            unset($this->creditmemoFees[$creditmemoFee->getEntityId()]);
        } catch (\Exception $e) {
            if ($creditmemoFee->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save credit memo fee with ID %1. Error: %2',
                        [$creditmemoFee->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new credit memo fee. Error: %1', $e->getMessage()));
        }

        return $creditmemoFee;
    }

    /**
     * @param int $entityId
     * @return ExtrafeeCreditmemoInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): ExtrafeeCreditmemoInterface
    {
        if (!isset($this->creditmemoFees[$entityId])) {
            $creditmemoFee = $this->feeCreditmemoFactory->create();
            $this->feeCreditmemoResource->load($creditmemoFee, $entityId);
            if (!$creditmemoFee->getEntityId()) {
                throw new NoSuchEntityException(__('Credit memo fee with specified ID "%1" not found.', $entityId));
            }
            $this->creditmemoFees[$entityId] = $creditmemoFee;
        }

        return $this->creditmemoFees[$entityId];
    }

    /**
     * @param ExtrafeeCreditmemoInterface $creditmemoFee
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ExtrafeeCreditmemoInterface $creditmemoFee): bool
    {
        try {
            $this->feeCreditmemoResource->delete($creditmemoFee);
            unset($this->creditmemoFees[$creditmemoFee->getEntityId()]);
        } catch (\Exception $e) {
            if ($creditmemoFee->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove credit memo fee with ID %1. Error: %2',
                        [$creditmemoFee->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove credit memo fee. Error: %1', $e->getMessage()));
        }

        return true;
    }
}
