<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\ExtrafeeInvoiceInterface;
use Amasty\Extrafee\Api\ExtrafeeInvoiceRepositoryInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeInvoice as FeeInvoiceResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ExtrafeeInvoiceRepository implements ExtrafeeInvoiceRepositoryInterface
{
    /**
     * @var FeeInvoiceResource
     */
    private $feeInvoiceResource;

    /**
     * @var ExtrafeeInvoiceFactory
     */
    private $feeInvoiceFactory;

    /**
     * Model data storage
     *
     * @var array
     */
    private $invoiceFees;

    public function __construct(FeeInvoiceResource $feeInvoiceResource, ExtrafeeInvoiceFactory $feeInvoiceFactory)
    {
        $this->feeInvoiceResource = $feeInvoiceResource;
        $this->feeInvoiceFactory = $feeInvoiceFactory;
    }

    /**
     * @param ExtrafeeInvoiceInterface $invoiceFee
     * @return ExtrafeeInvoiceInterface
     * @throws CouldNotSaveException
     */
    public function save(ExtrafeeInvoiceInterface $invoiceFee): ExtrafeeInvoiceInterface
    {
        try {
            if ($invoiceFee->getEntityId()) {
                $invoiceFee = $this->getById((int)$invoiceFee->getEntityId())->addData($invoiceFee->getData());
            }
            $this->feeInvoiceResource->save($invoiceFee);
            unset($this->invoiceFees[$invoiceFee->getEntityId()]);
        } catch (\Exception $e) {
            if ($invoiceFee->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save invoice fee with ID %1. Error: %2',
                        [$invoiceFee->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new invoice fee. Error: %1', $e->getMessage()));
        }

        return $invoiceFee;
    }

    /**
     * @param int $entityId
     * @return ExtrafeeInvoiceInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): ExtrafeeInvoiceInterface
    {
        if (!isset($this->invoiceFees[$entityId])) {
            $invoiceFee = $this->feeInvoiceFactory->create();
            $this->feeInvoiceResource->load($invoiceFee, $entityId);
            if (!$invoiceFee->getEntityId()) {
                throw new NoSuchEntityException(__('Invoice fee with specified ID "%1" not found.', $entityId));
            }
            $this->invoiceFees[$entityId] = $invoiceFee;
        }

        return $this->invoiceFees[$entityId];
    }

    /**
     * @param ExtrafeeInvoiceInterface $invoiceFee
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ExtrafeeInvoiceInterface $invoiceFee): bool
    {
        try {
            $this->feeInvoiceResource->delete($invoiceFee);
            unset($this->invoiceFees[$invoiceFee->getEntityId()]);
        } catch (\Exception $e) {
            if ($invoiceFee->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove invoice fee with ID %1. Error: %2',
                        [$invoiceFee->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove invoice fee. Error: %1', $e->getMessage()));
        }

        return true;
    }
}
