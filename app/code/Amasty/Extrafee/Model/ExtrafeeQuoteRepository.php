<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\ExtrafeeQuoteInterface;
use Amasty\Extrafee\Api\ExtrafeeQuoteRepositoryInterface;
use Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote as QuoteResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class ExtrafeeQuoteRepository implements ExtrafeeQuoteRepositoryInterface
{
    /**
     * @var QuoteResource
     */
    private $quoteResource;

    /**
     * @var ExtrafeeQuoteFactory
     */
    private $quoteFactory;

    /**
     * Model data storage
     *
     * @var array
     */
    private $quoteFees;

    public function __construct(QuoteResource $quoteResource, ExtrafeeQuoteFactory $quoteFactory)
    {
        $this->quoteResource = $quoteResource;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param ExtrafeeQuoteInterface $quoteFee
     * @return ExtrafeeQuoteInterface
     * @throws CouldNotSaveException
     */
    public function save(ExtrafeeQuoteInterface $quoteFee)
    {
        try {
            if ($quoteFee->getEntityId()) {
                $quoteFee = $this->getById($quoteFee->getEntityId())->addData($quoteFee->getData());
            }
            $this->quoteResource->save($quoteFee);
            unset($this->quoteFees[$quoteFee->getEntityId()]);
        } catch (\Exception $e) {
            if ($quoteFee->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save quoteFee with ID %1. Error: %2',
                        [$quoteFee->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new quoteFee. Error: %1', $e->getMessage()));
        }

        return $quoteFee;
    }

    /**
     * @param int $entityId
     * @return ExtrafeeQuoteInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getById($entityId)
    {
        if (!isset($this->quoteFees[$entityId])) {
            $quoteFee = $this->quoteFactory->create();
            $this->quoteResource->load($quoteFee, $entityId);
            if (!$quoteFee->getEntityId()) {
                throw new NoSuchEntityException(__('PaymentFee with specified ID "%1" not found.', $entityId));
            }
            $this->quoteFees[$entityId] = $quoteFee;
        }

        return $this->quoteFees[$entityId];
    }

    /**
     * @param ExtrafeeQuoteInterface $quoteFee
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ExtrafeeQuoteInterface $quoteFee)
    {
        try {
            $this->quoteResource->delete($quoteFee);
            unset($this->quoteFees[$quoteFee->getEntityId()]);
        } catch (\Exception $e) {
            if ($quoteFee->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove paymentFee with ID %1. Error: %2',
                        [$quoteFee->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove paymentFee. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($entityId)
    {
        $quoteFeeModel = $this->getById($entityId);
        $this->delete($quoteFeeModel);

        return true;
    }

    /**
     * @param int $quoteId
     * @param array $requiredFeeIds
     * @throws LocalizedException
     */
    public function checkChosenOptions($quoteId, $requiredFeeIds)
    {
        $chosenOptions = $this->quoteResource->getChosenOptions($quoteId, $requiredFeeIds);
        foreach ($requiredFeeIds as $feeId) {
            if (empty($chosenOptions[$feeId])) {
                throw new LocalizedException(__('Please select at least one option for fee.'));
            }
        }
    }
}
