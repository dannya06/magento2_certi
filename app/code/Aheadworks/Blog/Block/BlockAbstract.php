<?php
namespace Aheadworks\Blog\Block;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\TagInterface;
use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Aheadworks\Blog\Api\PostRepositoryInterface;
use Aheadworks\Blog\Api\TagRepositoryInterface;
use Aheadworks\Blog\Model\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Aheadworks\Blog\Model\Template\FilterProvider;

/**
 * Class BlockAbstract
 * @package Aheadworks\Blog\Block
 */
abstract class BlockAbstract extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var \Aheadworks\Blog\Model\Url
     */
    protected $url;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FilterProvider
     */
    protected $templateFilterProvider;

    /**
     * @var LinkFactory
     */
    protected $linkFactory;

    /**
     * Post url's cache
     *
     * @var array
     */
    private $postUrls = [];

    /**
     * BlockAbstract constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param PostRepositoryInterface $postRepository
     * @param TagRepositoryInterface $tagRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param \Aheadworks\Blog\Model\Url $url
     * @param Config $config
     * @param FilterProvider $templateFilterProvider
     * @param LinkFactory $linkFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CategoryRepositoryInterface $categoryRepository,
        PostRepositoryInterface $postRepository,
        TagRepositoryInterface $tagRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        \Aheadworks\Blog\Model\Url $url,
        Config $config,
        FilterProvider $templateFilterProvider,
        LinkFactory $linkFactory,
        array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->postRepository = $postRepository;
        $this->tagRepository = $tagRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->url = $url;
        $this->config = $config;
        $this->templateFilterProvider = $templateFilterProvider;
        $this->linkFactory = $linkFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieves current post
     *
     * @return PostInterface
     */
    public function getCurrentPost()
    {
        return $this->postRepository->get(
            $this->getRequest()->getParam('post_id')
        );
    }

    /**
     * Retrieves current category
     *
     * @return CategoryInterface
     */
    public function getCurrentCategory()
    {
        return $this->categoryRepository->get(
            $this->getRequest()->getParam('blog_category_id')
        );
    }

    /**
     * Retrieves current tag
     *
     * @return TagInterface
     */
    public function getCurrentTag()
    {
        return $this->tagRepository->getByName(
            $this->getRequest()->getParam('tag')
        );
    }
    /**
     * @param PostInterface $post
     * @return string
     */
    public function getPostUrl(PostInterface $post)
    {
        $postId = $post->getId();
        if (!isset($this->postUrls[$postId])) {
            $category = $this->getRequest()->getParam('blog_category_id')
                ? $this->getCurrentCategory()
                : null;
            $this->postUrls[$postId] = $this->url->getPostUrl($post, $category);
        }
        return $this->postUrls[$postId];
    }

    /**
     * @param TagInterface|string $tag
     * @return string
     */
    public function getSearchByTagUrl($tag)
    {
        return $this->url->getSearchByTagUrl($tag);
    }

    /**
     * Retrieves blog title
     *
     * @return string
     */
    protected function getBlogTitle()
    {
        return $this->config->getValue(Config::XML_GENERAL_BLOG_TITLE);
    }

    /**
     * Add crumbs
     *
     * @param \Magento\Theme\Block\Html\Breadcrumbs $breadCrumbsBlock
     * @param array $crumbs
     * @return void
     */
    protected function addCrumbs($breadCrumbsBlock, $crumbs = [])
    {
        $breadCrumbsBlock->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'link' => $this->_storeManager->getStore()->getBaseUrl()
            ]
        );
        if (!empty($crumbs)) {
            $breadCrumbsBlock->addCrumb(
                'blog_home',
                [
                    'label' => $this->getBlogTitle(),
                    'link' => $this->url->getBlogHomeUrl()
                ]
            );
            foreach ($crumbs as $crumb) {
                $breadCrumbsBlock->addCrumb($crumb['name'], $crumb['info']);
            }
        } else {
            $breadCrumbsBlock->addCrumb('blog_home', ['label' => $this->getBlogTitle()]);
        }
    }
}
