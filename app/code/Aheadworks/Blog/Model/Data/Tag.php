<?php
namespace Aheadworks\Blog\Model\Data;

/**
 * Tag data model.
 * @codeCoverageIgnore
 */
class Tag extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Aheadworks\Blog\Api\Data\TagInterface
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
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCount()
    {
        return (int)$this->_get(self::COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCount($count)
    {
        return $this->setData(self::COUNT, $count);
    }

    /**
     * {@inheritdoc}
     */
    public function getPostIds()
    {
        return $this->_get(self::POST_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostIds($postIds)
    {
        return $this->setData(self::POST_IDS, $postIds);
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
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\TagExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
