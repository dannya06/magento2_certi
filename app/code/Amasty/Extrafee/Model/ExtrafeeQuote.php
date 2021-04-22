<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\Data\ExtrafeeQuoteInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class ExtrafeeQuote extends AbstractModel implements ExtrafeeQuoteInterface, IdentityInterface
{
    /**
     * Fee cache tag
     */
    const CACHE_TAG = 'amasty_extrafee_quote';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ExtrafeeQuote::class);
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
    public function getEntityId()
    {
        return $this->_getData(self::ENTITY_ID);
    }

    /**
     * @param int $entityId
     * @return ExtrafeeQuoteInterface
     */
    public function setEntityId($entityId)
    {
        $this->setData(self::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * @return int
     */
    public function getQuoteId()
    {
        return $this->_getData(self::QUOTE_ID);
    }

    /**
     * @param int $quoteId
     * @return ExtrafeeQuoteInterface
     */
    public function setQuoteId($quoteId)
    {
        $this->setData(self::QUOTE_ID, $quoteId);

        return $this;
    }

    /**
     * @return int
     */
    public function getFeeId()
    {
        return $this->_getData(self::FEE_ID);
    }

    /**
     * @param int $feeId
     * @return ExtrafeeQuoteInterface
     */
    public function setFeeId($feeId)
    {
        $this->setData(self::FEE_ID, $feeId);

        return $this;
    }

    /**
     * @return int
     */
    public function getOptionId()
    {
        return $this->_getData(self::OPTION_ID);
    }

    /**
     * @param int $optionId
     * @return ExtrafeeQuoteInterface
     */
    public function setOptionId($optionId)
    {
        $this->setData(self::OPTION_ID, $optionId);

        return $this;
    }

    /**
     * @return float
     */
    public function getFeeAmount()
    {
        return (float)$this->_getData(self::FEE_AMOUNT);
    }

    /**
     * @param float $amount
     * @return ExtrafeeQuoteInterface
     */
    public function setFeeAmount($amount)
    {
        $this->setData(self::FEE_AMOUNT, $amount);

        return $this;
    }

    /**
     * @return float
     */
    public function getBaseFeeAmount()
    {
        return (float)$this->_getData(self::BASE_FEE_AMOUNT);
    }

    /**
     * @param float $baseAmount
     * @return ExtrafeeQuoteInterface
     */
    public function setBaseFeeAmount($baseAmount)
    {
        $this->setData(self::BASE_FEE_AMOUNT, $baseAmount);

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_getData(self::LABEL);
    }

    /**
     * @param string $label
     * @return ExtrafeeQuoteInterface
     */
    public function setLabel($label)
    {
        $this->setData(self::LABEL, $label);

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxAmount()
    {
        return (float)$this->_getData(self::TAX_AMOUNT);
    }

    /**
     * @param float $amount
     * @return ExtrafeeQuoteInterface
     */
    public function setTaxAmount($amount)
    {
        $this->setData(self::TAX_AMOUNT, $amount);

        return $this;
    }

    /**
     * @return float
     */
    public function getBaseTaxAmount()
    {
        return (float)$this->_getData(self::BASE_TAX_AMOUNT);
    }

    /**
     * @param float $baseAmount
     * @return ExtrafeeQuoteInterface
     */
    public function setBaseTaxAmount($baseAmount)
    {
        $this->setData(self::BASE_TAX_AMOUNT, $baseAmount);

        return $this;
    }
}
