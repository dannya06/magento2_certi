<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Blog\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Aheadworks\Blog\Block\Post\ListingFactory;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Model\Config;
use Aheadworks\Blog\Model\Url;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Blog\Model\Serialize\SerializeInterface;
use Aheadworks\Blog\Model\Serialize\Factory as SerializeFactory;
use Aheadworks\Blog\Model\Post\FeaturedImageInfo;

/**
 * Tag Cloud Widget
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RecentPost extends \Aheadworks\Blog\Block\Sidebar\Recent implements BlockInterface
{
    /**
     * @var string
     */
    const WIDGET_NAME_PREFIX = 'aw_blog_widget_recent_post_';

    /**
     * Path to template file in theme
     * @var string
     */
    protected $_template = 'Aheadworks_Blog::widget/recent_post/default.phtml';

    /**
     * @var SerializeInterface
     */
    private $serializer;
	
	/**
     * @var FeaturedImageInfo
     */
    protected $imageInfo;

    /**
     * @param Context $context
     * @param PostRepositoryInterface $postRepository
     * @param ListingFactory $postListingFactory
     * @param Config $config
     * @param SerializeFactory $serializeFactory
     * @param Url $url
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostRepositoryInterface $postRepository,
        ListingFactory $postListingFactory,
        Config $config,
        Url $url,
        SerializeFactory $serializeFactory,
		FeaturedImageInfo $imageInfo,
        array $data = []
    ) {
        parent::__construct($context, $postRepository, $postListingFactory, $config, $url, $data);
        $this->serializer = $serializeFactory->create();
		$this->imageInfo = $imageInfo;
    }

    /**
     * Is ajax request or not
     *
     * @return bool
     */
    public function isAjax()
    {
        return $this->_request->isAjax();
    }

    /**
     * {@inheritdoc}
     */
    public function getPosts($numberToDisplay = null)
    {
        return parent::getPosts($this->getData('number_to_display'));
    }

    /**
     * Checks blog is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->isBlogEnabled();
    }

    /**
     * Retrieve widget encode data
     *
     * @return string
     */
    public function getWidgetEncodeData()
    {
        return base64_encode(
            $this->serializer->serialize(
                [
                    'name' => $this->getNameInLayout(),
                    'number_to_display' => $this->getData('number_to_display'),
                    'title' => $this->getData('title'),
                    'template' => $this->getTemplate()
                ]
            )
        );
    }
	
	/**
     * Get featured image url
     *
     * @return string
     */
    public function getFeaturedImageUrl($image)
    {
        return $this->imageInfo->getImageUrl($image);
    }
	

    /**
     * {@inheritdoc}
     */
    public function getNameInLayout()
    {
        return self::WIDGET_NAME_PREFIX . parent::getNameInLayout();
    }
}
