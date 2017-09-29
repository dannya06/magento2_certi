<?php
namespace Aheadworks\Blog\Block\Widget;

use Aheadworks\Blog\Api\Data\TagInterface;
use Aheadworks\Blog\Api\TagManagementInterface;
use Aheadworks\Blog\Model\Config;

/**
 * Tag Cloud Widget
 */
class TagCloud extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var TagManagementInterface
     */
    private $tagManagement;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Aheadworks\Blog\Model\Url
     */
    private $url;

    /**
     * @var int
     */
    private $minCount = 0;

    /**
     * @var int
     */
    private $maxCount = 0;

    /**
     * @var float
     */
    private $minWeightDefault = 0.72;

    /**
     * @var float
     */
    private $maxWeightDefault = 1.28;

    /**
     * @var float
     */
    private $slopeDefault = 0.1;

    /**
     * Tags cache
     *
     * @var TagInterface[]|null
     */
    private $tags = null;

    /**
     * TagCloud constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param TagManagementInterface $tagManagement
     * @param Config $config
     * @param \Aheadworks\Blog\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        TagManagementInterface $tagManagement,
        Config $config,
        \Aheadworks\Blog\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->tagManagement = $tagManagement;
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * Retrieves max weight
     *
     * @return float
     */
    private function getMaxWeight()
    {
        return $this->getData('max_weight') ? : $this->maxWeightDefault;
    }

    /**
     * Retrieves min weight
     *
     * @return float
     */
    private function getMinWeight()
    {
        return $this->getData('min_weight') ? : $this->minWeightDefault;
    }

    /**
     * Retrieves slope
     *
     * @return float
     */
    private function getSlope()
    {
        return $this->getData('slope') ? : $this->slopeDefault;
    }

    /**
     * @return TagInterface[]
     */
    public function getTags()
    {
        if ($this->tags === null) {
            $this->tags = $this->tagManagement
                ->getCloudTags(
                    $this->_storeManager->getStore()->getId(),
                    $this->getRequest()->getParam('blog_category_id')
                )
                ->getItems();
            if (count($this->tags)) {
                $this->minCount = $this->tags[0]->getCount();
                $this->maxCount = $this->tags[count($this->tags) - 1]->getCount();
            }
        }
        return $this->tags;
    }

    /**
     * @return bool
     */
    public function isCloud()
    {
        return (bool)$this->config->getValue(Config::XML_SIDEBAR_HIGHLIGHT_TAGS);
    }

    /**
     * @param TagInterface $tag
     * @return int
     */
    public function getTagWeight(TagInterface $tag)
    {
        $count = $tag->getCount();
        $averageCount = (int)($this->maxCount + $this->minCount) / 2;

        $weightOffset = $count >= $averageCount ? $this->getMaxWeight() : $this->getMinWeight();
        $countOffset = $averageCount - $this->getSlope() / ($weightOffset - 1);
        $weight = $weightOffset - $this->getSlope() / ($count - $countOffset);

        return round($weight, 2) * 100;
    }

    /**
     * @param TagInterface|string $tag
     * @return string
     */
    public function getSearchByTagUrl($tag)
    {
        return $this->url->getSearchByTagUrl($tag);
    }
}
