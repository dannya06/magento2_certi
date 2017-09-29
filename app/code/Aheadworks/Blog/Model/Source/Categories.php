<?php
namespace Aheadworks\Blog\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class Categories
 * @package Aheadworks\Blog\Model\Source
 */
class Categories extends \Magento\Framework\DataObject implements OptionSourceInterface
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\Collection
     */
    private $categoryCollection;

    /**
     * Categories constructor.
     *
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->categoryCollection = $categoryCollectionFactory->create();
        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->categoryCollection->toOptionArray();
    }
}
