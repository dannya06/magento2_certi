<?php

/**
 * Converting product configurable images on product page
 * 
 * @author Icube Developer <fiko@icube.us>
 * @since 2.0.1
 */

namespace Jajuma\WebpImages\Block\Product\Renderer;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{
    /** @var \Jajuma\WebpImages\Helper\Data */
    protected $helperJajuma;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $swatchMediaHelper,
        \Jajuma\WebpImages\Helper\Data $helperJajuma,
        array $data = [],
        \Magento\Swatches\Model\SwatchAttributesProvider $swatchAttributesProvider = null,
        \Magento\Catalog\Model\Product\Image\UrlBuilder $imageUrlBuilder = null
    ) {
        $this->helperJajuma = $helperJajuma;
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $helper,
            $catalogProduct,
            $currentCustomer,
            $priceCurrency,
            $configurableAttributeData,
            $swatchHelper,
            $swatchMediaHelper,
            $data,
            $swatchAttributesProvider,
            $imageUrlBuilder
        );
    }

    protected function getOptionImages()
    {
        $result = parent::getOptionImages();
        
        if (count($result) === 0) {
            return $result;
        }

        foreach ($result as $id => $images) {
            if (count($images) === 0) {
                continue;
            }
            foreach ($images as $key => $image) {
                if (isset($image['thumb']) && $image['thumb'] != '') {
                    $result[$id][$key]['thumb'] = $this->helperJajuma->convert($image['thumb']);
                }
                if (isset($image['img']) && $image['img'] != '') {
                    $result[$id][$key]['img'] = $this->helperJajuma->convert($image['img']);
                }
                if (isset($image['full']) && $image['full'] != '') {
                    $result[$id][$key]['full'] = $this->helperJajuma->convert($image['full']);
                }
            }
        }

        return $result;
    }
}
