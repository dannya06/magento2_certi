<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessor;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\CatalogPriceCalculator;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessorInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterfaceFactory;
use Magento\Bundle\Pricing\Price\FinalPrice;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceInfoInterface;

/**
 * Class Bundle
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessor
 */
class Bundle implements TypeProcessorInterface
{
    /**
     * @var EarnItemInterfaceFactory
     */
    private $earnItemFactory;

    /**
     * @var CatalogPriceCalculator
     */
    private $catalogPriceCalculator;

    /**
     * @param EarnItemInterfaceFactory $earnItemFactory
     * @param CatalogPriceCalculator $catalogPriceCalculator
     */
    public function __construct(
        EarnItemInterfaceFactory $earnItemFactory,
        CatalogPriceCalculator $catalogPriceCalculator
    ) {
        $this->earnItemFactory = $earnItemFactory;
        $this->catalogPriceCalculator = $catalogPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getEarnItems($product, $beforeTax = true)
    {
        $earnItems = [];

        if ($product->getPriceType() == AbstractType::CALCULATE_PARENT) {
            $price = $this->catalogPriceCalculator->getFinalPriceAmount(
                $product,
                $product->getFinalPrice(),
                $beforeTax
            );
        } else {
            /** @var PriceInfoInterface $priceInfo */
            $priceInfo = $product->getPriceInfo();
            /** @var PriceInterface|FinalPrice $finalPrice */
            $finalPrice = $priceInfo->getPrice('final_price');
            /** @var AmountInterface $maximumPrice */
            $maximumPrice = $finalPrice->getMaximalPrice();
            $maximumPriceValue = $beforeTax
                ? $maximumPrice->getValue(['tax'])
                : $maximumPrice->getValue();

            $price = $maximumPriceValue;
        }

        /** @var EarnItemInterface $earnItem */
        $earnItem = $this->earnItemFactory->create();
        $earnItem
            ->setProductId($product->getId())
            ->setBaseAmount($price)
            ->setQty(1);

        $earnItems[] = $earnItem;
        
        return $earnItems;
    }
}
