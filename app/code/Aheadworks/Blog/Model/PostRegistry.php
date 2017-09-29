<?php
namespace Aheadworks\Blog\Model;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Registry for \Aheadworks\Blog\Model\Post
 */
class PostRegistry
{
    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var array
     */
    private $postRegistryById = [];

    /**
     * @var array
     */
    private $postRegistryByUrlKey = [];

    /**
     * PostRegistry constructor.
     * @param PostFactory $postFactory
     */
    public function __construct(PostFactory $postFactory)
    {
        $this->postFactory = $postFactory;
    }

    /**
     * Retrieve Post Model from registry by ID
     *
     * @param int $postId
     * @return Post
     * @throws NoSuchEntityException
     */
    public function retrieve($postId)
    {
        if (!isset($this->postRegistryById[$postId])) {
            /** @var Post $post */
            $post = $this->postFactory->create();
            $post->load($postId);
            if (!$post->getId()) {
                throw NoSuchEntityException::singleField('postId', $postId);
            } else {
                $this->postRegistryById[$postId] = $post;
                $this->postRegistryByUrlKey[$post->getUrlKey()] = $post;
            }
        }
        return $this->postRegistryById[$postId];
    }

    /**
     * Retrieve Post Model from registry by URL-Key
     *
     * @param string $urlKey URL-Key
     * @return Post
     * @throws NoSuchEntityException
     */
    public function retrieveByUrlKey($urlKey)
    {
        if (!isset($this->postRegistryByUrlKey[$urlKey])) {
            /** @var Post $post */
            $post = $this->postFactory->create();
            $post->loadByUrlKey($urlKey);
            if (!$post->getId()) {
                throw NoSuchEntityException::singleField('urlKey', $urlKey);
            } else {
                $this->postRegistryById[$post->getId()] = $post;
                $this->postRegistryByUrlKey[$urlKey] = $post;
            }
        }
        return $this->postRegistryByUrlKey[$urlKey];
    }

    /**
     * Remove instance of the Post Model from registry by ID
     *
     * @param int $postId
     * @return void
     */
    public function remove($postId)
    {
        if (isset($this->postRegistryById[$postId])) {
            /** @var Post $post */
            $post = $this->postRegistryById[$postId];
            unset($this->postRegistryById[$postId]);
            unset($this->postRegistryByUrlKey[$post->getUrlKey()]);
        }
    }

    /**
     * Remove instance of the Post Model from registry by URL-Key
     *
     * @param string $urlKey URL-Key
     * @return void
     */
    public function removeByUrlKey($urlKey)
    {
        if (isset($this->postRegistryByUrlKey[$urlKey])) {
            /** @var Post $post */
            $post = $this->postRegistryByUrlKey[$urlKey];
            unset($this->postRegistryById[$post->getId()]);
            unset($this->postRegistryByUrlKey[$urlKey]);
        }
    }

    /**
     * Replace existing Post Model with a new one.
     *
     * @param Post $post
     * @return $this
     */
    public function push(Post $post)
    {
        $this->postRegistryById[$post->getId()] = $post;
        $this->postRegistryByUrlKey[$post->getUrlKey()] = $post;
        return $this;
    }
}
