<?php
namespace Aheadworks\Blog\Model;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Registry for \Aheadworks\Blog\Model\Category
 */
class CategoryRegistry
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var array
     */
    private $categoryRegistryById = [];

    /**
     * @var array
     */
    private $categoryRegistryByUrlKey = [];

    /**
     * CategoryRegistry constructor.
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Retrieve Category Model from registry by ID
     *
     * @param int $categoryId
     * @return Category
     * @throws NoSuchEntityException
     */
    public function retrieve($categoryId)
    {
        if (!isset($this->categoryRegistryById[$categoryId])) {
            /** @var Category $category */
            $category = $this->categoryFactory->create();
            $category->load($categoryId);
            if (!$category->getId()) {
                throw NoSuchEntityException::singleField('categoryId', $categoryId);
            } else {
                $this->categoryRegistryById[$categoryId] = $category;
                $this->categoryRegistryByUrlKey[$category->getUrlKey()] = $category;
            }
        }
        return $this->categoryRegistryById[$categoryId];
    }

    /**
     * Retrieve Category Model from registry by URL-Key
     *
     * @param string $urlKey URL-Key
     * @return Category
     * @throws NoSuchEntityException
     */
    public function retrieveByUrlKey($urlKey)
    {
        if (!isset($this->categoryRegistryByUrlKey[$urlKey])) {
            /** @var Category $category */
            $category = $this->categoryFactory->create();
            $category->loadByUrlKey($urlKey);
            if (!$category->getId()) {
                throw NoSuchEntityException::singleField('urlKey', $urlKey);
            } else {
                $this->categoryRegistryById[$category->getId()] = $category;
                $this->categoryRegistryByUrlKey[$urlKey] = $category;
            }
        }
        return $this->categoryRegistryByUrlKey[$urlKey];
    }

    /**
     * Remove instance of the Category Model from registry by ID
     *
     * @param int $categoryId
     * @return void
     */
    public function remove($categoryId)
    {
        if (isset($this->categoryRegistryById[$categoryId])) {
            /** @var Category $category */
            $category = $this->categoryRegistryById[$categoryId];
            unset($this->categoryRegistryById[$categoryId]);
            unset($this->categoryRegistryByUrlKey[$category->getUrlKey()]);
        }
    }

    /**
     * Remove instance of the Category Model from registry by URL-Key
     *
     * @param string $urlKey URL-Key
     * @return void
     */
    public function removeByUrlKey($urlKey)
    {
        if (isset($this->categoryRegistryByUrlKey[$urlKey])) {
            /** @var Category $category */
            $category = $this->categoryRegistryByUrlKey[$urlKey];
            unset($this->categoryRegistryById[$category->getId()]);
            unset($this->categoryRegistryByUrlKey[$urlKey]);
        }
    }

    /**
     * Replace existing Category Model with a new one.
     *
     * @param Category $category
     * @return $this
     */
    public function push(Category $category)
    {
        $this->categoryRegistryById[$category->getId()] = $category;
        $this->categoryRegistryByUrlKey[$category->getUrlKey()] = $category;
        return $this;
    }
}
