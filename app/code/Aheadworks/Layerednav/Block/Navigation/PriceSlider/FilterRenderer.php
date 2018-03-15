<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block\Navigation\PriceSlider;

use Aheadworks\Layerednav\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\LayeredNavigation\Block\Navigation\FilterRendererInterface;

/**
 * Class FilterRenderer
 * @package Aheadworks\Layerednav\Block\Navigation\PriceSlider
 */
class FilterRenderer extends Template implements FilterRendererInterface
{
    /**
     * Min value
     */
    const MIN_VALUE = 0.01;

    /**
     * @var Layer
     */
    private $layer;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Layerednav::layer/renderer/price_slider/filter.phtml';

    /**
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param Config $config
     * @param PriceCurrencyInterface $priceCurrency
     * @param FormatInterface $localeFormat
     * @param ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        Config $config,
        PriceCurrencyInterface $priceCurrency,
        FormatInterface $localeFormat,
        ResolverInterface $localeResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layer = $layerResolver->get();
        $this->config = $config;
        $this->priceCurrency = $priceCurrency;
        $this->localeFormat = $localeFormat;
        $this->localeResolver = $localeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function render(FilterInterface $filter)
    {
        $priceFilterSet = $otherFilterSet = false;
        $converter = $this->priceCurrency;
        $priceData = $filter->getMinMaxPrices();

        foreach ($this->layer->getState()->getFilters() as $layerFilter) {
            if ($filter->getRequestVar() == $layerFilter->getFilter()->getRequestVar()) {
                list($fromPrice, $toPrice) = explode('-', $layerFilter->getValue());
                $priceData['fromPrice'] = (double)$fromPrice;
                $priceData['toPrice'] = (double)$toPrice;
                $priceFilterSet = true;
            } else {
                $otherFilterSet = true;
            }
        }

        $priceData['minPrice'] = floor($converter->convertAndRound($priceData['minPrice']));
        $priceData['maxPrice'] = ceil($converter->convertAndRound($priceData['maxPrice']));

        if (!$priceFilterSet) {
            $priceData['minPrice'] = floor($converter->convertAndRound($priceData['minSelectionPrice']));
            $priceData['maxPrice'] = ceil($converter->convertAndRound($priceData['maxSelectionPrice']));
            $priceData['fromPrice'] = $priceData['minPrice'];
            $priceData['toPrice'] = $priceData['maxPrice'];
        } elseif ($otherFilterSet) {
            if ($priceData['minSelectionPrice'] > $priceData['fromPrice']
            ) {
                $priceData['minPrice'] = floor(
                    $converter->convertAndRound($priceData['fromPrice'] - self::MIN_VALUE)
                );
                $priceData['minPrice'] < 0 ? 0 : $priceData['minPrice'];
            }
            if ($priceData['maxSelectionPrice'] < $priceData['toPrice']) {
                $priceData['maxPrice'] = ceil(
                    $converter->convertAndRound($priceData['toPrice'] + self::MIN_VALUE)
                );
            }
        }

        $priceData['priceFormat'] = $this->localeFormat->getPriceFormat();

        $this->assign($priceData);
        $html = $this->_toHtml();
        return $html;
    }

    /**
     * Is price slider enabled.
     *
     * @return bool
     */
    public function isPriceSliderEnabled()
    {
        return $this->config->isPriceSliderEnabled();
    }

    /**
     * Is from-to inputs enabled
     *
     * @return bool
     */
    public function isFromToInputsEnabled()
    {
        return $this->config->isPriceFromToEnabled();
    }

    /**
     * Is filter button disabled.
     * If popover is enabled, price filter updates immediately on change
     *
     * @return bool
     */
    public function isFilterButtonDisabled()
    {
        return $this->config->isAjaxEnabled() && !$this->config->isPopoverDisabled();
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->priceCurrency->getCurrencySymbol();
    }

    /**
     * Check if currency symbol should be displayed
     *
     * @return bool
     */
    public function isDisplayCurrencySymbol()
    {
        $format = $this->getCurrencyFormat();
        $symPlaceholderPos = iconv_strpos($format, '¤');
        return $symPlaceholderPos !== false;
    }

    /**
     * Check if currency symbol should displayed after value
     *
     * @return bool
     * @throws \Zend_Locale_Exception
     */
    public function isCurrencySymAfterValue()
    {
        $format = $this->getCurrencyFormat();
        $symPlaceholderPos = iconv_strpos($format, '¤');
        return $symPlaceholderPos > 0;
    }

    /**
     * Get currency format
     *
     * @return string
     * @throws \Zend_Locale_Exception
     */
    private function getCurrencyFormat()
    {
        return \Zend_Locale_Data::getContent($this->localeResolver->getLocale(), 'currencynumber');
    }
}
