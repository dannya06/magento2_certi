<?php
namespace WeltPixel\CategoryPage\Block\Product;

use Magento\Catalog\Helper\ImageFactory as HelperFactory;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{

    /** @var  \WeltPixel\CategoryPage\Helper\Data */
    protected $categoryPageHelper;

    /**
     * @var \WeltPixel\OwlCarouselSlider\Helper\Custom
     */
    protected $owlHelperCustom;

    /**
     * @param HelperFactory $helperFactory
     * @param \Magento\Catalog\Block\Product\ImageFactory $imageFactory
     * @param \WeltPixel\CategoryPage\Helper\Data $categoryPageHelper
     * @param \WeltPixel\OwlCarouselSlider\Helper\Custom $owlHelperCustom
     */
    public function __construct(
        HelperFactory $helperFactory,
        \Magento\Catalog\Block\Product\ImageFactory $imageFactory,
        \WeltPixel\CategoryPage\Helper\Data $categoryPageHelper,
        \WeltPixel\OwlCarouselSlider\Helper\Custom $owlHelperCustom
    ) {
        $this->categoryPageHelper = $categoryPageHelper;
        $this->owlHelperCustom = $owlHelperCustom;
        parent::__construct($helperFactory, $imageFactory);
    }



    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create()
    {
        $hoverImageIds = [];

        /** Check if owlcarousel's hover is enabled */
        if ($this->owlHelperCustom->isHoverImageEnabled()) {
            $hoverImageIds[] = 'related_products_list';
            $hoverImageIds[] = 'upsell_products_list';
            $hoverImageIds[] = 'cart_cross_sell_products';
            $hoverImageIds[] = 'new_products_content_widget_grid';
        }

        /** Check if product listing hover is enabled */
        if ($this->categoryPageHelper->isHoverImageEnabled()) {
            $hoverImageIds[] = 'category_page_grid';
            $hoverImageIds[] = 'category_page_list';
        }

        if (empty($hoverImageIds)) {
            return parent::create();
        }

        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);

        $template = $helper->getFrame()
            ? 'Magento_Catalog::product/image.phtml'
            : 'Magento_Catalog::product/image_with_borders.phtml';

        $imagesize = $helper->getResizedImageInfo();

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $helper->getUrl(),
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' =>  $this->getRatio($helper),
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth(),
                'resized_image_height' => !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight(),
            ],
        ];

        if (in_array($this->imageId, $hoverImageIds)) {
            /** @var \Magento\Catalog\Helper\Image $helper */
            $hoverHelper = $this->helperFactory->create()
                ->init($this->product, $this->imageId . '_hover')->resize($helper->getWidth(), $helper->getHeight());

            $hoverImageUrl = $hoverHelper->getUrl();
            $placeHolderUrl =  $hoverHelper->getDefaultPlaceholderUrl();

            /** Do not display hover placeholder */
            if ($placeHolderUrl == $hoverImageUrl) {
                $data['data']['hover_image_url'] = NULL;
            } else {
                $data['data']['hover_image_url'] = $hoverImageUrl;
            }


            $template = $helper->getFrame()
                ? 'WeltPixel_CategoryPage::product/image.phtml'
                : 'WeltPixel_CategoryPage::product/image_with_borders.phtml';

            $data['data']['template'] = $template;
        }


        return $this->imageFactory->create($data);
    }
}