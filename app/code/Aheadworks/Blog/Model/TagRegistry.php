<?php
namespace Aheadworks\Blog\Model;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Registry for \Aheadworks\Blog\Model\Tag
 */
class TagRegistry
{
    /**
     * @var TagFactory
     */
    private $tagFactory;

    /**
     * @var array
     */
    private $tagRegistryById = [];

    /**
     * @var array
     */
    private $tagRegistryByName = [];

    /**
     * TagRegistry constructor.
     * @param TagFactory $tagFactory
     */
    public function __construct(TagFactory $tagFactory)
    {
        $this->tagFactory = $tagFactory;
    }

    /**
     * Retrieve Tag Model from registry by ID
     *
     * @param int $tagId
     * @return Tag
     * @throws NoSuchEntityException
     */
    public function retrieve($tagId)
    {
        if (!isset($this->tagRegistryById[$tagId])) {
            /** @var Tag $tag */
            $tag = $this->tagFactory->create();
            $tag->load($tagId);
            if (!$tag->getId()) {
                throw NoSuchEntityException::singleField('tagId', $tagId);
            } else {
                $this->tagRegistryById[$tagId] = $tag;
                $this->tagRegistryByName[$tag->getName()] = $tag;
            }
        }
        return $this->tagRegistryById[$tagId];
    }

    /**
     * Retrieve Tag Model from registry by name
     *
     * @param string $name name
     * @return Tag
     * @throws NoSuchEntityException
     */
    public function retrieveByName($name)
    {
        if (!isset($this->tagRegistryByName[$name])) {
            /** @var Tag $tag */
            $tag = $this->tagFactory->create();
            $tag->loadByName($name);
            if (!$tag->getId()) {
                throw NoSuchEntityException::singleField('name', $name);
            } else {
                $this->tagRegistryById[$tag->getId()] = $tag;
                $this->tagRegistryByName[$name] = $tag;
            }
        }
        return $this->tagRegistryByName[$name];
    }

    /**
     * Remove instance of the Tag Model from registry by ID
     *
     * @param int $tagId
     * @return void
     */
    public function remove($tagId)
    {
        if (isset($this->tagRegistryById[$tagId])) {
            /** @var Tag $tag */
            $tag = $this->tagRegistryById[$tagId];
            unset($this->tagRegistryById[$tagId]);
            unset($this->tagRegistryByName[$tag->getName()]);
        }
    }

    /**
     * Remove instance of the Tag Model from registry by name
     *
     * @param string $name name
     * @return void
     */
    public function removeByName($name)
    {
        if (isset($this->tagRegistryByName[$name])) {
            /** @var Tag $tag */
            $tag = $this->tagRegistryByName[$name];
            unset($this->tagRegistryById[$tag->getId()]);
            unset($this->tagRegistryByName[$name]);
        }
    }

    /**
     * Replace existing Tag Model with a new one.
     *
     * @param Tag $tag
     * @return $this
     */
    public function push(Tag $tag)
    {
        $this->tagRegistryById[$tag->getId()] = $tag;
        $this->tagRegistryByName[$tag->getName()] = $tag;
        return $this;
    }
}
