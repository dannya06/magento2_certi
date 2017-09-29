<?php
namespace Aheadworks\Blog\Model;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Api\Data\TagInterface;
use Aheadworks\Blog\Controller\Router;

/**
 * Class Url
 * @package Aheadworks\Blog\Model
 */
class Url
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * Url constructor.
     *
     * @param Config $config
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        Config $config,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieves url.
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    private function getUrl($route, $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }

    /**
     * Retrieves blog home url.
     *
     * @return string
     */
    public function getBlogHomeUrl()
    {
        return $this->getUrl(null, ['_direct' => $this->getRouteToBlog()]);
    }

    /**
     * Retrieves post url.
     *
     * @param PostInterface $post
     * @param CategoryInterface|null $category
     * @return string
     */
    public function getPostUrl(PostInterface $post, CategoryInterface $category = null)
    {
        $parts = [$this->getRouteToBlog()];
        if ($category !== null) {
            $parts[] = $category->getUrlKey();
        }
        $parts[] = $post->getUrlKey();
        return $this->getUrl(null, ['_direct' => implode('/', $parts)]);
    }

    /**
     * @param PostInterface $post
     * @return string
     */
    public function getPostRoute(PostInterface $post)
    {
        return $this->getRouteToBlog() . '/' . $post->getUrlKey();
    }

    /**
     * Retrieves post url.
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryUrl(CategoryInterface $category)
    {
        return $this->getUrl(null, ['_direct' => $this->getCategoryRoute($category)]);
    }

    /**
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryRoute(CategoryInterface $category)
    {
        return $this->getRouteToBlog() . '/' . $category->getUrlKey();
    }

    /**
     * Retrieves search by tag url.
     *
     * @param TagInterface|string $tag
     * @return string
     */
    public function getSearchByTagUrl($tag)
    {
        $tagName = $tag instanceof TagInterface ? $tag->getName() : $tag;
        return $this->getUrl(
            null,
            ['_direct' => $this->getRouteToBlog() . '/' . Router::TAG_KEY . '/' . urlencode($tagName)]
        );
    }

    /**
     * @return string
     */
    protected function getRouteToBlog()
    {
        return $this->config->getValue(Config::XML_GENERAL_ROUTE_TO_BLOG);
    }
}
