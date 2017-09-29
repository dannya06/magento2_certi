<?php
namespace Aheadworks\Blog\Api\Data;

/**
 * Post interface.
 * @api
 */
interface PostInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const URL_KEY = 'url_key';
    const TITLE = 'title';
    const SHORT_CONTENT = 'short_content';
    const CONTENT = 'content';
    const STATUS = 'status';
    const VIRTUAL_STATUS = 'virtual_status';
    const AUTHOR_ID = 'author_id';
    const AUTHOR_NAME = 'author_name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PUBLISH_DATE = 'publish_date';
    const IS_ALLOW_COMMENTS = 'is_allow_comments';
    const STORE_IDS = 'store_ids';
    const CATEGORY_IDS = 'category_ids';
    const TAGS = 'tags';
    const META_TITLE = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const PUBLISHED_COMMENTS = 'published_comments';
    const NEW_COMMENTS = 'new_comments';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get URL-Key
     *
     * @return string
     */
    public function getUrlKey();

    /**
     * Set URL-Key
     *
     * @param string $urlKey
     * @return $this
     */
    public function setUrlKey($urlKey);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get short content
     *
     * @return string|null
     */
    public function getShortContent();

    /**
     * Set short content
     *
     * @param string $shortContent
     * @return $this
     */
    public function setShortContent($shortContent);

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get virtual status
     *
     * @return string|null
     */
    public function getVirtualStatus();

    /**
     * Set virtual status
     *
     * @param string $virtualStatus
     * @return string
     */
    public function setVirtualStatus($virtualStatus);

    /**
     * Get author ID
     *
     * @return int|null
     */
    public function getAuthorId();

    /**
     * Set author ID
     *
     * @param int $authorId
     * @return $this
     */
    public function setAuthorId($authorId);

    /**
     * Get author name
     *
     * @return string|null
     */
    public function getAuthorName();

    /**
     * Set author name
     *
     * @param string $authorName
     * @return $this
     */
    public function setAuthorName($authorName);

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set update time
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get publish date
     *
     * @return string|null
     */
    public function getPublishDate();

    /**
     * Set publish date
     *
     * @param string $publishDate
     * @return $this
     */
    public function setPublishDate($publishDate);

    /**
     * Get is allowed comments
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsAllowComments();

    /**
     * Set is allowed comments
     *
     * @param bool $isAllowComments
     * @return $this
     */
    public function setIsAllowComments($isAllowComments);

    /**
     * Get store IDs
     *
     * @return int[]
     */
    public function getStoreIds();

    /**
     * Set store IDs
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * Get category IDs
     *
     * @return int[]|null
     */
    public function getCategoryIds();

    /**
     * Set category IDs
     *
     * @param int[] $categoryIds
     * @return $this
     */
    public function setCategoryIds($categoryIds);

    /**
     * Get tag names
     *
     * @return string[]|null
     */
    public function getTags();

    /**
     * Set tag names
     *
     * @param string[] $tags
     * @return $this
     */
    public function setTags($tags);

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return $this
     */
    public function setMetaTitle($metaTitle);

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return $this
     */
    public function setMetaDescription($metaDescription);

    /**
     * Get published comments count
     *
     * @return int|null
     */
    public function getPublishedComments();

    /**
     * Set published comments count
     *
     * @param int $publishedComments
     * @return $this
     */
    public function setPublishedComments($publishedComments);

    /**
     * Get new comments count
     *
     * @return int|null
     */
    public function getNewComments();

    /**
     * Set new comments count
     *
     * @param int $newComments
     * @return $this
     */
    public function setNewComments($newComments);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Aheadworks\Blog\Api\Data\PostExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Aheadworks\Blog\Api\Data\PostExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\PostExtensionInterface $extensionAttributes);
}
