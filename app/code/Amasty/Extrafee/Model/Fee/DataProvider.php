<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Model\Fee;

use Amasty\Base\Model\Serializer;
use Amasty\Extrafee\Model\Fee;
use Amasty\Extrafee\Model\ResourceModel\Fee\Collection;
use Amasty\Extrafee\Model\ResourceModel\Fee\CollectionFactory;
use Amasty\Extrafee\Model\StoresSorter;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /** @var Collection */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /** @var  array */
    protected $stores;
    /**
     * @var Serializer
     */
    private $serializerBase;

    /**
     * @var StoresSorter
     */
    private $storesSorter;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $feeCollectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        Serializer $serializerBase,
        StoresSorter $storesSorter,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $feeCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        $this->storesSorter = $storesSorter;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
        $this->serializerBase = $serializerBase;
    }

    /**
     * @return mixed
     */
    public function getStores()
    {
        if ($this->stores === null) {
            $this->stores = $this->storeManager->getStores(true);
        }
        return $this->stores;
    }

    /**
     * @return array
     */
    public function getStoresSortedBySortOrder()
    {
        return $this->storesSorter->getStoresSortedBySortOrder($this->getStores());
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        $metaOptions = &$meta['options']['children']['rows']['children']['record']['children'];
        /** @var Store $store */
        foreach ($this->getStoresSortedBySortOrder() as $store) {
            $validation = [];
            $label = $store->getName();
            $required = false;
            if ($store->getId() == Store::DEFAULT_STORE_ID) {
                $label = __($label);
                $required = true;
                $validation['required-entry'] = true;
            }

            $metaOptions['store_' . $store->getId()] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'componentType' => 'field',
                            'label' => $label,
                            'required' => $required,
                            'validation' => $validation
                        ]
                    ]
                ]
            ];
        }

        $metaOptions['remove'] = [
            'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'actionDelete',
                            'dataType' => 'text',
                            'fit' => true
                        ]
                    ]
            ]
        ];

        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var Fee $fee*/
        foreach ($items as $fee) {
            $data = $fee->getData();

            $options = $this->serializerBase->unserialize($data['options_serialized']);

            if (is_array($options)) {
                $data['options'] = $options;
            }
            $this->loadedData[$fee->getId()] = $data;
        }

        return $this->loadedData;
    }
}
