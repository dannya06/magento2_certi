<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product\Type\Giftcard;

use Aheadworks\Giftcard\Api\Data\OptionInterface;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\Price as CatalogPrice;

/**
 * Class Price
 *
 * @package Aheadworks\Giftcard\Model\Product\Type\Giftcard
 */
class Price extends CatalogPrice
{
    /**
     * {@inheritdoc}
     */
    public function getBasePrice($product, $qty = null)
    {
        return $this->applyAmounts($product, (float)$product->getPrice());
    }

    /**
     * Retrieve product open amount min
     *
     * @param Product $product
     * @return []
     */
    public function getOpenAmountMin(Product $product)
    {
        if ($amount = $product->getData(ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MIN)) {
            return (float)$amount;
        }
        return false;
    }

    /**
     * Retrieve product open amount max
     *
     * @param Product $product
     * @return []
     */
    public function getOpenAmountMax(Product $product)
    {
        if ($amount = $product->getData(ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MAX)) {
            return (float)$amount;
        }
        return false;
    }

    /**
     * Retrieve product amounts
     *
     * @param Product $product
     * @return []
     */
    public function getAmounts(Product $product)
    {
        return $product->getTypeInstance()->getAmounts($product);
    }

    /**
     * Apply Gift Card amounts for product
     *
     * @param Product $product
     * @param float $price
     * @return float
     */
    private function applyAmounts(Product $product, $price)
    {
        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption(OptionInterface::AMOUNT);
            if ($customOption) {
                $price += $customOption->getValue();
            }
        }
        return $price;
    }
}
