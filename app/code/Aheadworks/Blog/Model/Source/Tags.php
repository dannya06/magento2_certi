<?php
namespace Aheadworks\Blog\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Blog\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;

/**
 * Class Tags
 * @package Aheadworks\Blog\Model\Source
 */
class Tags extends \Magento\Framework\DataObject implements OptionSourceInterface
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Tag\Collection
     */
    private $tagCollection;

    /**
     * Tags constructor.
     *
     * @param TagCollectionFactory $tagCollectionFactory
     * @param array $data
     */
    public function __construct(
        TagCollectionFactory $tagCollectionFactory,
        array $data = []
    ) {
        $this->tagCollection = $tagCollectionFactory->create();
        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->tagCollection->toOptionArray();
    }
}
