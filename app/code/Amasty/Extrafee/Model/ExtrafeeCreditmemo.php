<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\ExtrafeeCreditmemoInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class ExtrafeeCreditmemo extends AbstractModel implements ExtrafeeCreditmemoInterface, IdentityInterface
{
    /**
     * Fee cache tag
     */
    const CACHE_TAG = 'amasty_extrafee_creditmemo';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ExtrafeeCreditmemo::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return (int)$this->_getData(self::ENTITY_ID);
    }

    /**
     * @param int $entityId
     *
     * @return void
     */
    public function setEntityId($entityId)
    {
        $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return (int)$this->_getData(self::ORDER_ID);
    }

    /**
     * @param int $orderId
     *
     * @return void
     */
    public function setOrderId(int $orderId)
    {
        $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @return int
     */
    public function getCreditmemoId(): int
    {
        return (int)$this->_getData(self::CREDITMEMO_ID);
    }

    /**
     * @param int $creditmemoId
     *
     * @return void
     */
    public function setCreditmemoId(int $creditmemoId)
    {
        $this->setData(self::CREDITMEMO_ID, $creditmemoId);
    }

    /**
     * @return int
     */
    public function getFeeId(): int
    {
        return (int)$this->_getData(self::FEE_ID);
    }

    /**
     * @param int $feeId
     *
     * @return void
     */
    public function setFeeId(int $feeId)
    {
        $this->setData(self::FEE_ID, $feeId);
    }

    /**
     * @return int
     */
    public function getOptionId(): int
    {
        return (int)$this->_getData(self::OPTION_ID);
    }

    /**
     * @param int $optionId
     *
     * @return void
     */
    public function setOptionId(int $optionId)
    {
        $this->setData(self::OPTION_ID, $optionId);
    }

    /**
     * @return float
     */
    public function getBaseTotalAmount(): float
    {
        return (float)$this->_getData(self::BASE_TOTAL);
    }

    /**
     * @param float $total
     *
     * @return void
     */
    public function setBaseTotalAmount($total)
    {
        $this->setData(self::BASE_TOTAL, $total);
    }

    /**
     * @return float
     */
    public function getTotalAmount(): float
    {
        return (float)$this->_getData(self::TOTAL);
    }

    /**
     * @param float $total
     *
     * @return void
     */
    public function setTotalAmount($total)
    {
        $this->setData(self::TOTAL, $total);
    }

    /**
     * @return float
     */
    public function getBaseTaxAmount(): float
    {
        return (float)$this->_getData(self::BASE_TAX);
    }

    /**
     * @param float $tax
     *
     * @return void
     */
    public function setBaseTaxAmount($tax)
    {
        $this->setData(self::BASE_TAX, $tax);
    }

    /**
     * @return float
     */
    public function getTaxAmount(): float
    {
        return (float)$this->_getData(self::TAX);
    }

    /**
     * @param float $tax
     *
     * @return void
     */
    public function setTaxAmount($tax)
    {
        $this->setData(self::TAX, $tax);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->_getData(self::LABEL);
    }

    /**
     * @param string $label
     *
     * @return void
     */
    public function setFeeLabel($label)
    {
        $this->setData(self::LABEL, $label);
    }

    /**
     * @return string
     */
    public function getOptionLabel(): string
    {
        return $this->_getData(self::OPTION_LABEL);
    }

    /**
     * @param string $label
     *
     * @return void
     */
    public function setFeeOptionLabel($label)
    {
        $this->setData(self::OPTION_LABEL, $label);
    }
}
