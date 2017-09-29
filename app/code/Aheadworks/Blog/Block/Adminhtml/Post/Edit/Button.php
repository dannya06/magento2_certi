<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit;

use Aheadworks\Blog\Api\PostRepositoryInterface;

/**
 * Class Button
 * @package Aheadworks\Blog\Block\Adminhtml\Post\Edit
 */
abstract class Button
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * Button constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param PostRepositoryInterface $postRepository
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        PostRepositoryInterface $postRepository
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->postRepository = $postRepository;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }

    /**
     * Retrieves request post ID
     *
     * @return int|null
     */
    protected function getPostId()
    {
        return $this->request->getParam('post_id');
    }
}
