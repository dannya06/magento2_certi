<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model;

use Aheadworks\Layerednav\Model\Template\FilterProvider;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\ProductList\Toolbar;
use Magento\Framework\View\Result\Page;

/**
 * Class PageConfig
 * @package Aheadworks\Layerednav\Model
 */
class PageConfig
{
    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var FilterProvider
     */
    private $templateFilterProvider;

    /**
     * @var Toolbar
     */
    private $toolbar;

    /**
     * @var UrlManager
     */
    private $urlManager;

    /**
     * @var Layer
     */
    private $layer;

    /**
     * @param PageTypeResolver $pageTypeResolver
     * @param Config $config
     * @param FilterProvider $templateFilterProvider
     * @param Toolbar $toolbar
     * @param UrlManager $urlManager
     * @param Resolver $layerResolver
     */
    public function __construct(
        PageTypeResolver $pageTypeResolver,
        Config $config,
        FilterProvider $templateFilterProvider,
        Toolbar $toolbar,
        UrlManager $urlManager,
        Resolver $layerResolver
    ) {
        $this->pageTypeResolver = $pageTypeResolver;
        $this->config = $config;
        $this->templateFilterProvider = $templateFilterProvider;
        $this->toolbar = $toolbar;
        $this->urlManager = $urlManager;
        $this->layer = $layerResolver->get();
    }

    /**
     * Apply layered navigation options to result page
     *
     * @param Page $page
     * @return void
     */
    public function apply(Page $page)
    {
        $pageConfig = $page->getConfig();
        $pageType = $this->pageTypeResolver->getType();
        if ($pageType == PageTypeResolver::PAGE_TYPE_CATALOG_SEARCH
            && $this->config->isDisableIndexingOnCatalogSearch()
        ) {
            $pageConfig->setMetadata('robots', 'NOINDEX,FOLLOW');
        }
        if ($pageType == PageTypeResolver::PAGE_TYPE_CATEGORY) {
            $templateFilter = $this->templateFilterProvider->getFilter();
            $pageConfig->setMetadata(
                'description',
                $templateFilter->filter($this->config->getPageMetaDescriptionTemplate())
            );

            if ($this->config->isRewriteMetaRobotsTagEnabled()
                && ($this->toolbar->getCurrentPage() > 1
                    || $this->toolbar->getDirection()
                    || $this->toolbar->getLimit()
                    || $this->toolbar->getMode()
                    || $this->toolbar->getOrder()
                    || $this->hasMultipleFilterSelection()
                )
            ) {
                $pageConfig->setMetadata('robots', 'NOINDEX,NOFOLLOW');
            }

            if ($this->config->isAddCanonicalUrlsEnabled()) {
                $pageConfig->addRemotePageAsset(
                    $this->urlManager->getCurrentCanonicalUrl(),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }
        }
    }

    /**
     * Check if there is a multiple filter selection
     *
     * @return bool
     */
    private function hasMultipleFilterSelection()
    {
        foreach ($this->layer->getState()->getFilters() as $filterItem) {
            $values = explode(',', $filterItem->getValue());
            if (count($values) > 1) {
                return true;
            }
        }
        return false;
    }
}
