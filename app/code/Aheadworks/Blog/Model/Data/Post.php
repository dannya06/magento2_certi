<?php
namespace Aheadworks\Blog\Model\Data;

/**
 * Post data model.
 * @codeCoverageIgnore
 */
class Post extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Aheadworks\Blog\Api\Data\PostInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlKey()
    {
        return $this->_get(self::URL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortContent()
    {
        return $this->_get(self::SHORT_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setShortContent($shortContent)
    {
        return $this->setData(self::SHORT_CONTENT, $shortContent);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->_get(self::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getVirtualStatus()
    {
        return $this->_get(self::VIRTUAL_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setVirtualStatus($virtualStatus)
    {
        return $this->setData(self::VIRTUAL_STATUS, $virtualStatus);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorId()
    {
        return $this->_get(self::AUTHOR_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorId($authorId)
    {
        return $this->setData(self::AUTHOR_ID, $authorId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorName()
    {
        return $this->_get(self::AUTHOR_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorName($authorName)
    {
        return $this->setData(self::AUTHOR_NAME, $authorName);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishDate()
    {
        return $this->_get(self::PUBLISH_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishDate($publishDate)
    {
        return $this->setData(self::PUBLISH_DATE, $publishDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAllowComments()
    {
        return (bool)$this->_get(self::IS_ALLOW_COMMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAllowComments($isAllowComments)
    {
        return $this->setData(self::IS_ALLOW_COMMENTS, $isAllowComments);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return $this->_get(self::STORE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryIds()
    {
        return $this->_get(self::CATEGORY_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryIds($categoryIds)
    {
        return $this->setData(self::CATEGORY_IDS, $categoryIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->_get(self::TAGS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTags($tags)
    {
        return $this->setData(self::TAGS, $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->_get(self::META_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->_get(self::META_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedComments()
    {
        return $this->_get(self::PUBLISHED_COMMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishedComments($publishedComments)
    {
        return $this->setData(self::PUBLISHED_COMMENTS, $publishedComments);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewComments()
    {
        return $this->_get(self::NEW_COMMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setNewComments($newComments)
    {
        return $this->setData(self::NEW_COMMENTS, $newComments);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\PostExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
