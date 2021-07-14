<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */



namespace Amasty\Extrafee\Model;

use Amasty\Extrafee\Api\TaxExtrafeeDetailsInterface;
use Magento\Framework\DataObject;

class TaxExtrafeeDetails extends DataObject implements TaxExtrafeeDetailsInterface
{
    /**
     * @return string|null
     */
    public function getItems()
    {
        return $this->getData(TaxExtrafeeDetailsInterface::ITEMS);
    }

    /**
     * @param string $items
     * @return $this|TaxExtrafeeDetailsInterface
     */
    public function setItems($items)
    {
        $this->setData(TaxExtrafeeDetailsInterface::ITEMS, $items);

        return $this;
    }

    /**
     * @return array|null
     */
    public function getValueExclTax()
    {
        return $this->getData(TaxExtrafeeDetailsInterface::VALUE_EXCL_TAX);
    }

    /**
     * @param float $amountExclTax
     * @return $this|TaxExtrafeeDetailsInterface
     */
    public function setValueExclTax($amountExclTax)
    {
        $this->setData(TaxExtrafeeDetailsInterface::VALUE_EXCL_TAX, $amountExclTax);

        return$this;
    }

    /**
     * @return float|null
     */
    public function getValueInclTax()
    {
        return $this->getData(TaxExtrafeeDetailsInterface::VALUE_INCL_TAX);
    }

    /**
     * @param float $amoutInclTax
     * @return $this|TaxExtrafeeDetailsInterface
     */
    public function setValueInclTax($amoutInclTax)
    {
        $this->setData(TaxExtrafeeDetailsInterface::VALUE_INCL_TAX, $amoutInclTax);

        return $this;
    }
}
