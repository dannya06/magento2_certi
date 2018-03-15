<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\CacheInterface;

/**
 * Class CategoryList
 * @package Aheadworks\Layerednav\App\Request
 */
class CategoryList
{
    /**
     * @var string
     */
    const CATEGORIES_CACHE_KEY = 'aw_ln_categories';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Category[]
     */
    private $categories;

    /**
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param CacheInterface $cache
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        CacheInterface $cache
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->cache = $cache;
    }

    /**
     * Get categories keyed by url-key
     *
     * @return array
     */
    public function getCategoriesKeyedByUrlKey()
    {
        $result = [];
        foreach ($this->getCategories() as $category) {
            $result[$category['url_key']] = $category;
        }
        return $result;
    }

    /**
     * Get categories keyed by Id
     *
     * @return array
     */
    public function getCategoriesKeyedById()
    {
        $result = [];
        foreach ($this->getCategories() as $category) {
            $result[$category['id']] = $category;
        }
        return $result;
    }

    /**
     * Get category url-keys
     *
     * @return array
     */
    public function getCategoryUrlKeys()
    {
        $result = [];
        foreach ($this->getCategories() as $category) {
            $result[] = $category['url_key'];
        }
        return $result;
    }

    /**
     * Get categories
     *
     * @return array
     */
    private function getCategories()
    {
        if (null === $this->categories) {
            $storeId = $this->storeManager->getStore()->getId();
            $cacheId = self::CATEGORIES_CACHE_KEY . '_' . $storeId;

            $categories = $this->cache->load($cacheId);
            $this->categories = $categories
                ? unserialize($categories)
                : null;

            if (null === $this->categories) {
                $this->categories = $this->loadCategories($storeId);
                $this->cache->save(serialize($this->categories), $cacheId, [], null);
            }
        }
        return $this->categories;
    }

    /**
     * Load categories from DB
     *
     * @param int $storeId
     * @return array
     */
    private function loadCategories($storeId)
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->addAttributeToSelect('url_key')
            ->setStoreId($storeId)
            ->addIsActiveFilter();

        $categoriesArray = [];
        /** @var Category $category */
        foreach ($collection->getItems() as $category) {
            $categoriesArray[] = [
                'id' => $category->getId(),
                'url_key' => $category->getUrlKey()
            ];
        }

        return $categoriesArray;
    }
}
