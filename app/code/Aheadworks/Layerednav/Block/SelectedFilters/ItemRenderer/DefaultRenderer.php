<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block\SelectedFilters\ItemRenderer;

use Aheadworks\Layerednav\Block\SelectedFilters\ItemRendererInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\SelectedList as ItemsList;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Framework\View\Element\Template;

/**
 * Class DefaultRenderer
 * @package Aheadworks\Layerednav\Block\SelectedFilters\ItemRenderer
 */
class DefaultRenderer extends Template implements ItemRendererInterface
{
    /**
     * @var ItemsList
     */
    private $itemsList;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'layer/selected_filters/renderer/default.phtml';

    /**
     * @param Template\Context $context
     * @param ItemsList $itemsList
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ItemsList $itemsList,
        array $data = []
    ) {
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
     */
    public function getItemHtmlId(FilterItem $item)
    {
        return 'aw-filter-option-' . $item->getFilter()->getRequestVar() . '-' . $item->getValue();
    }

    /**
     * Get item label
     *
     * @param FilterItem $item
     * @return string
     */
    public function getLabel(FilterItem $item)
    {
        return sprintf(
            '%s%s',
            $this->itemsList->hasSame($item)
            ? __($item->getFilter()->getName()) . ': '
            : '',
            __($item->getLabel())
        );
    }
}
