<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Template;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory;

/**
 * Class VariablesProvider
 * @package Aheadworks\Layerednav\Model\Template
 */
class VariablesProvider
{
    /**
     * @var Layer
     */
    private $layer;

    /**
     * @var Factory
     */
    private $dataObjectFactory;

    /**
     * @param Resolver $layerResolver
     * @param Factory $dataObjectFactory
     */
    public function __construct(
        Resolver $layerResolver,
        Factory $dataObjectFactory
    ) {
        $this->layer = $layerResolver->get();
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Get template variables
     *
     * @return array
     */
    public function getVariables()
    {
        return [
            'category' => $this->getCategoryVar(),
            'urls' => $this->getUrlsVar()
        ];
    }

    /**
     * Get category template variable
     *
     * @return DataObject
     */
    private function getCategoryVar()
    {
        $category = $this->layer->getCurrentCategory();
        return $this->dataObjectFactory->create(
            [
                'name' => $category->getName(),
                'metatitle' => $category->getMetaTitle()
            ]
        );
    }

    /**
     * Get urls template variable
     *
     * @return DataObject
     */
    private function getUrlsVar()
    {
        $allFilters = [];
        $filterItems = $this->layer->getState()->getFilters();
        foreach ($filterItems as $item) {
            $allFilters[] = $this->dataObjectFactory->create(
                [
                    'name' => $item->getFilter()->getName(),
                    'value' => $item->getValueString()
                ]
            );
        }

        return $this->dataObjectFactory->create(
            ['all_filters' => $allFilters]
        );
    }
}
