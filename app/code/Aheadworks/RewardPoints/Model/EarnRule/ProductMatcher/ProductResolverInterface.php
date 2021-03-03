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
namespace Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher;

use Magento\Catalog\Model\Product;

/**
 * Interface ProductResolverInterface
 * @package Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher
 */
interface ProductResolverInterface
{
    /**
     * Get products for validation
     *
     * @param Product $product
     * @return Product[]
     */
    public function getProductsForValidation($product);
}
