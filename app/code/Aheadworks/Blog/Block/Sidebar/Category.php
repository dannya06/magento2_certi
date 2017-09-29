<?php
namespace Aheadworks\Blog\Block\Sidebar;

use Aheadworks\Blog\Helper\Config;

/**
 * Sidebar Cms block
 * @package Aheadworks\Blog\Block\Sidebar
 */
class Category extends \Aheadworks\Blog\Block\Sidebar
{
    /**
     * @var \Aheadworks\Blog\Helper\CmsBlock
     */
    protected $cmsBlockHelper;
    protected $categoryCollectionFactory;
    protected $categoryAllCollection = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Aheadworks\Blog\Helper\Url $urlHelper
     * @param Config $configHelper
     * @param \Aheadworks\Blog\Helper\CmsBlock $cmsBlockHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Aheadworks\Blog\Helper\Url $urlHelper,
        \Aheadworks\Blog\Helper\Config $configHelper,
        \Aheadworks\Blog\Helper\CmsBlock $cmsBlockHelper,
        \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlHelper,
            $configHelper,
            $data
        );
        $this->cmsBlockHelper = $cmsBlockHelper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;

    }

    public function getCategoryAllCollection()
    {
        if ($this->categoryAllCollection === null) {
            $this->categoryAllCollection = $this->categoryCollectionFactory->create()
                ->addEnabledFilter()
                ->addStoreFilter($this->_storeManager->getStore()->getId());
        }
        return $this->categoryAllCollection;
    }

    public function getCategoryLinkHtml($category)
    {
        return $this->urlHelper->getCategoryUrl($category);
	}
 

}
