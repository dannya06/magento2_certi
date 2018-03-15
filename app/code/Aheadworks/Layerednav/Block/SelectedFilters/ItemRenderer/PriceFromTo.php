<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block\SelectedFilters\ItemRenderer;

use Aheadworks\Layerednav\Block\SelectedFilters\ItemRendererInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\SelectedList as ItemsList;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class PriceFromTo
 * @package Aheadworks\Layerednav\Block\SelectedFilters\ItemRenderer
 */
class PriceFromTo extends Template implements ItemRendererInterface
{
    /**
     * @var ItemsList
     */
    private $itemsList;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'layer/selected_filters/renderer/default.phtml';

    /**
     * @param Template\Context $context
     * @param ItemsList $itemsList
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ItemsList $itemsList,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->itemsList = $itemsList;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(FilterItem $item)
    {
        $this->assign('filterItem', $item);
        $html = $this->_toHtml();
        $this->assign('filterItem', null);
        return $html;
    }

    /**
     * Get filter item html Id
     *
     * @param FilterItem $item
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getItemHtmlId(FilterItem $item)
    {
        return 'aw-filter-option-price';
    }

    /**
     * Get item label
     *
     * @param FilterItem $item
     * @return string
     */
    public function getLabel(FilterItem $item)
    {
        $labelParts = [];
        $value = explode('-', $item->getValueString());
        foreach ($value as $valuePart) {
            $labelParts[] = $this->priceCurrency->format($valuePart, false);
        }
        return sprintf(
            '%s%s',
            $this->itemsList->hasSame($item)
            ? __($item->getFilter()->getName()) . ': '
            : '',
            implode('-', $labelParts)
        );
    }
}
