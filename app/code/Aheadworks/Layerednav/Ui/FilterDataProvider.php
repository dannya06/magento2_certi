<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\Filter\FormDataProcessor as FilterFormDataProcessor;
use Magento\Framework\Api\Filter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Store\Model\Store;

/**
 * Class FilterDataProvider
 * @package Aheadworks\Layerednav\Ui
 */
class FilterDataProvider extends AbstractDataProvider
{
    /**
     * @var string
     */
    private $requestScopeFieldName;

    /**
     * Layered navigation filter key
     */
    const FILTER_PERSISTOR_KEY = 'aw_layerednav_filter';

    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var FilterFormDataProcessor
     */
    private $filterFormDataProcessor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param string $requestScopeFieldName
     * @param FilterRepositoryInterface $filterRepository
     * @param DataPersistorInterface $dataPersistor
     * @param DataObjectProcessor $dataObjectProcessor
     * @param RequestInterface $request
     * @param FilterFormDataProcessor $filterFormDataProcessor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        $requestScopeFieldName,
        FilterRepositoryInterface $filterRepository,
        DataPersistorInterface $dataPersistor,
        DataObjectProcessor $dataObjectProcessor,
        RequestInterface $request,
        FilterFormDataProcessor $filterFormDataProcessor,
        array $meta = [],
        array $data = []
    ) {
        $this->requestScopeFieldName = $requestScopeFieldName;
        $this->filterRepository = $filterRepository;
        $this->dataPersistor = $dataPersistor;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->request = $request;
        $this->filterFormDataProcessor = $filterFormDataProcessor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get(self::FILTER_PERSISTOR_KEY);
        if (!empty($dataFromForm)) {
            if (isset($dataFromForm['id'])) {
                $data[$dataFromForm['id']] = $dataFromForm;
            } else {
                $data[null] = $dataFromForm;
            }
            $this->dataPersistor->clear(self::FILTER_PERSISTOR_KEY);
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            if ($id) {
                $storeId = $this->request->getParam($this->requestScopeFieldName, Store::DEFAULT_STORE_ID);

                /** @var FilterInterface $filter */
                $filter = $this->filterRepository->get($id, $storeId);

                $data[$filter->getId()] = $this->filterFormDataProcessor->getPreparedFormData($filter, $storeId);
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }
}
