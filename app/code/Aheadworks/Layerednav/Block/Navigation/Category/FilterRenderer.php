<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block\Navigation\Category;

use Aheadworks\Layerednav\Model\Layer\Filter\Category;
use Aheadworks\Layerednav\Model\Config;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\LayeredNavigation\Block\Navigation\FilterRendererInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryFactory;

/**
 * Class FilterRenderer
 * @package Aheadworks\Layerednav\Block\Navigation\Category\FilterRenderer
 */
class FilterRenderer extends Template implements FilterRendererInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Layerednav::layer/renderer/category/filter.phtml';

    /**
     * @var Layer
     */
    private $layer;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Data
     */
    private $catalogData;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @var array
     */
    private $categories;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param Config $config
     * @param Data $catalogData
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        Config $config,
        Data $catalogData,
        CategoryRepositoryInterface $categoryRepository,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layer = $layerResolver->get();
        $this->config = $config;
        $this->catalogData = $catalogData;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function render(FilterInterface $filter)
    {
        $this->filter = $filter;
        $this->assign('filter', $filter);
        $this->assign('filterItems', $filter->getItems());
        $this->assign('categories', $this->getCategories());
        $html = $this->_toHtml();
        $this->assign('filter', null);
        $this->assign('filterItems', []);
        $this->assign('categories', []);
        return $html;
    }

    /**
     * Check if filter item is active
     *
     * @param FilterItem $item
     * @return bool
     * @throws LocalizedException
     */
    public function isActiveItem(FilterItem $item)
    {
        foreach ($this->layer->getState()->getFilters() as $filter) {
            if ($filter->getFilter()->getRequestVar() == $item->getFilter()->getRequestVar()) {
                $filterValues = explode(',', $filter->getValue());
                if (false !== array_search($item->getValue(), $filterValues)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if need to to show item
     *
     * @param FilterItem $item
     * @return bool
     * @throws LocalizedException
     */
    public function isNeedToShowItem(FilterItem $item)
    {
        return !$this->config->hideEmptyAttributeValues()
            || ($this->isActiveItem($item))
            || ($item->getCount());
    }

    /**
     * Get filter items count to display
     *
     * @param FilterItem[] $filterItems
     * @return int
     * @throws LocalizedException
     */
    private function getDisplayItemsCount($filterItems)
    {
        $count = 0;
        foreach ($filterItems as $filterItem) {
            if ($this->isNeedToShowItem($filterItem)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get count of show more items
     *
     * @param FilterItem[] $filterItems
     * @return int
     * @throws LocalizedException
     */
    public function getShowMoreCount($filterItems)
    {
        $displayLimit = $this->config->getFilterValuesDisplayLimit();
        $itemsCount = $this->getDisplayItemsCount($filterItems);
        if (isset($displayLimit) && $displayLimit > 0 && $itemsCount > $displayLimit) {
            return $itemsCount - $displayLimit;
        }
        return 0;
    }

    /**
     * Get filter values display limit
     *
     * @return int
     */
    public function getDisplayLimit()
    {
        return $this->config->getFilterValuesDisplayLimit();
    }

    /**
     * Get categories
     *
     * @return array
     */
    public function getCategories()
    {
        if (!$this->categories) {
            $this->categories = $this->catalogData->getBreadcrumbPath();
            foreach ($this->categories as $index => $category) {
                if (!$category['link']) {
                    $this->categories[$index]['link'] = '#';
                }
            }
        }

        return $this->categories;
    }

    /**
     * Get category class
     *
     * @param string $categoryIndex
     * @return string
     * @throws LocalizedException
     */
    public function getCategoryClass($categoryIndex)
    {
        $categoryClass = '';
        if ($this->isActiveCategory($categoryIndex)) {
            $categoryClass = 'current active';
        } elseif ($this->isCurrentCategory($categoryIndex)) {
            $categoryClass = 'current';
        }
        return $categoryClass;
    }

    /**
     * Is Active category
     *
     * @param string $categoryIndex
     * @return bool
     * @throws LocalizedException
     */
    public function isActiveCategory($categoryIndex)
    {
        if ($this->isCurrentCategory($categoryIndex)) {
            foreach ($this->layer->getState()->getFilters() as $filter) {
                if ($filter->getFilter()->getRequestVar() == Category::REQUEST_VAR) {
                    $filterValues = explode(',', $filter->getValue());
                    if ($filterValues) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Is Current category
     *
     * @param string $categoryIndex
     * @return bool
     */
    public function isCurrentCategory($categoryIndex)
    {
        $categories = $this->getCategories();
        end($categories);

        if ($categoryIndex == key($categories)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the category has children categories
     *
     * @param int $categoryId
     * @return bool
     */
    public function hasChildrenCategories($categoryId)
    {
        try {
            /** @var CategoryInterface|\Magento\Catalog\Model\Category $category */
            $category = is_numeric($categoryId)
                ? $this->categoryRepository->get($categoryId)
                : $this->getCategoryByUrlKey($categoryId);
            return $category->hasChildren();
        } catch (NoSuchEntityException $e) {
            // do nothing
        }
        return false;
    }

    /**
     * Get category url
     *
     * @param int $categoryId
     * @return string
     */
    public function getCategoryUrl($categoryId)
    {
        try {
            /** @var CategoryInterface|\Magento\Catalog\Model\Category $category */
            $category = is_numeric($categoryId)
                ? $this->categoryRepository->get($categoryId)
                : $this->getCategoryByUrlKey($categoryId);
            return $category->getUrl();
        } catch (NoSuchEntityException $e) {
            // do nothing
        }
        return '';
    }

    /**
     * @param string $urlKey
     * @return \Magento\Framework\DataObject
     */
    private function getCategoryByUrlKey($urlKey)
    {
        $category = $this->categoryFactory->create()
            ->addAttributeToFilter('url_key', $urlKey)
            ->addUrlRewriteToResult()
            ->getFirstItem();
        return $category;
    }
}
