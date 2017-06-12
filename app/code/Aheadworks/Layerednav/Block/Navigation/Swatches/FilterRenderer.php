<?php
namespace Aheadworks\Layerednav\Block\Navigation\Swatches;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;

/**
 * Class FilterRenderer
 * @package Aheadworks\Layerednav\Block\Navigation\Swatches
 */
class FilterRenderer extends RenderLayered
{
    /**
     * @var Layer
     */
    private $layer;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Layerednav::layer/renderer/swatches/filter.phtml';

    /**
     * @param Context $context
     * @param Attribute $eavAttribute
     * @param AttributeFactory $layerAttribute
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Swatches\Helper\Media $mediaHelper
     * @param LayerResolver $layerResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        LayerResolver $layerResolver,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $eavAttribute,
            $layerAttribute,
            $swatchHelper,
            $mediaHelper,
            $data
        );
        $this->layer = $layerResolver->get();
    }

    /**
     * Check if filter item is active
     *
     * @param string $code
     * @param string $value
     * @return bool
     * @throws LocalizedException
     */
    public function isActiveItem($code, $value)
    {
        foreach ($this->layer->getState()->getFilters() as $filter) {
            $filterValues = explode(',', $filter->getValue());
            if ($filter->getFilter()->getRequestVar() == $code
                && false !== array_search($value, $filterValues)
            ) {
                return true;
            }
        }
        return false;
    }
}
