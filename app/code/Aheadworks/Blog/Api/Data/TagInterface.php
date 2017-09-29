<?php
namespace Aheadworks\Blog\Api\Data;

/**
 * Tag interface.
 */
interface TagInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const COUNT = 'count';
    const POST_IDS = 'post_ids';
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
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get count
     *
     * @return int|null
     */
    public function getCount();

    /**
     * Set count
     *
     * @param int $count
     * @return $this
     */
    public function setCount($count);

    /**
     * Get post IDs
     *
     * @return int[]
     */
    public function getPostIds();

    /**
     * Set post IDs
     *
     * @param int[] $postIds
     * @return $this
     */
    public function setPostIds($postIds);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Aheadworks\Blog\Api\Data\TagExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Aheadworks\Blog\Api\Data\TagExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aheadworks\Blog\Api\Data\TagExtensionInterface $extensionAttributes);
}
