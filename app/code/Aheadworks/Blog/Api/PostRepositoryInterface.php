<?php
namespace Aheadworks\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Post CRUD interface.
 * @api
 */
interface PostRepositoryInterface
{
    /**
     * Save post.
     *
     * @param \Aheadworks\Blog\Api\Data\PostInterface $post
     * @return \Aheadworks\Blog\Api\Data\PostInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Blog\Api\Data\PostInterface $post);

    /**
     * Retrieve post.
     *
     * @param int $postId
     * @return \Aheadworks\Blog\Api\Data\PostInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($postId);

    /**
     * Retrieve post by URL-Key.
     *
     * @param string $urlKey
     * @return \Aheadworks\Blog\Api\Data\PostInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByUrlKey($urlKey);

    /**
     * Retrieve posts matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param string[] $additionalFields
     * @return \Aheadworks\Blog\Api\Data\PostSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, array $additionalFields = []);

    /**
     * Delete post.
     *
     * @param \Aheadworks\Blog\Api\Data\PostInterface $post
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\Blog\Api\Data\PostInterface $post);

    /**
     * Delete post by ID.
     *
     * @param int $postId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($postId);
}
