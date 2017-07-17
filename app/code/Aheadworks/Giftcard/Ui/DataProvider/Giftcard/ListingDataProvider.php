<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\DataProvider\Giftcard;

use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Collection;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\Giftcard\Ui\DataProvider\Giftcard
 */
class ListingDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()
                ->load();
        }
        return parent::getData();
    }
}
