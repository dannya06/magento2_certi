<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\View\LayoutInterface;

/**
 * Class PageTypeResolver
 * @package Aheadworks\Layerednav\Model
 */
class PageTypeResolver
{
    /**
     * Default page type
     */
    const PAGE_TYPE_DEFAULT = 'default';

    /**
     * Category page
     */
    const PAGE_TYPE_CATEGORY = 'category';

    /**
     * Catalog search page
     */
    const PAGE_TYPE_CATALOG_SEARCH = 'catalog_search';

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var array
     */
    private $pageHandles = [
        self::PAGE_TYPE_CATEGORY => 'catalog_category_view',
        self::PAGE_TYPE_CATALOG_SEARCH => 'catalogsearch_result_index'
    ];

    /**
     * @var array
     */
    private $pageLayers = [
        self::PAGE_TYPE_CATEGORY => Resolver::CATALOG_LAYER_CATEGORY,
        self::PAGE_TYPE_CATALOG_SEARCH => Resolver::CATALOG_LAYER_SEARCH
    ];

    /**
     * @param LayoutInterface $layout
     * @param array $pageHandles
     * @param array $pageLayers
     */
    public function __construct(
        LayoutInterface $layout,
        $pageHandles = [],
        $pageLayers = []
    ) {
        $this->layout = $layout;
        $this->pageHandles = array_merge($this->pageHandles, $pageHandles);
        $this->pageLayers = array_merge($this->pageLayers, $pageLayers);
    }

    /**
     * Get page type
     *
     * @return string
     */
    public function getType()
    {
        $handles = $this->layout->getUpdate()->getHandles();
        foreach ($this->pageHandles as $pageType => $pageHandle) {
            if (in_array($pageHandle, $handles)) {
                return $pageType;
            }
        }
        return self::PAGE_TYPE_DEFAULT;
    }

    /**
     * Get layer type
     *
     * @param string|null $pageType
     * @return string
     */
    public function getLayerType($pageType = null)
    {
        $pageType = $pageType ? : $this->getType();
        return isset($this->pageLayers[$pageType])
            ? $this->pageLayers[$pageType]
            : Resolver::CATALOG_LAYER_CATEGORY;
    }
}
