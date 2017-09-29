<?php
namespace Aheadworks\Blog\Ui\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;

/**
 * Class AbstractDataProvider
 * @package Aheadworks\Blog\Ui\DataProvider
 */
abstract class AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $primaryFieldName;

    /**
     * @var string
     */
    protected $requestFieldName;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var SearchCriteria
     */
    protected $searchCriteria;

    /**
     * AbstractDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObjectProcessor $dataObjectProcessor
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObjectProcessor $dataObjectProcessor,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->name = $name;
        $this->primaryFieldName = $primaryFieldName;
        $this->requestFieldName = $requestFieldName;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->request = $request;
        $this->meta = $meta;
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getConfigData()
    {
        return isset($this->data['config']) ? $this->data['config'] : [];
    }

    /**
     * @inheritdoc
     */
    public function setConfigData($config)
    {
        $this->data['config'] = $config;
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @inheritdoc
     */
    public function getFieldMetaInfo($fieldSetName, $fieldName)
    {
        return isset($this->meta[$fieldSetName]['fields'][$fieldName])
            ? $this->meta[$fieldSetName]['fields'][$fieldName]
            : [];
    }

    /**
     * @inheritdoc
     */
    public function getFieldSetMetaInfo($fieldSetName)
    {
        return isset($this->meta[$fieldSetName]) ? $this->meta[$fieldSetName] : [];
    }

    /**
     * @inheritdoc
     */
    public function getFieldsMetaInfo($fieldSetName)
    {
        return isset($this->meta[$fieldSetName]['fields'])
            ? $this->meta[$fieldSetName]['fields']
            : [];
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryFieldName()
    {
        return $this->primaryFieldName;
    }

    /**
     * @inheritdoc
     */
    public function getRequestFieldName()
    {
        return $this->requestFieldName;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->searchCriteriaBuilder->addFilters([$filter]);
    }

    /**
     * @inheritdoc
     */
    public function addOrder($field, $direction)
    {
        $sortOrder = new SortOrder(
            [
                SortOrder::FIELD => $field,
                SortOrder::DIRECTION => $direction
            ]
        );
        $this->searchCriteriaBuilder->setSortOrders([$sortOrder]);
    }

    /**
     * @inheritdoc
     */
    public function setLimit($offset, $size)
    {
        $this->searchCriteriaBuilder->setPageSize($size);
        $this->searchCriteriaBuilder->setCurrentPage($offset);
    }

    /**
     * @inheritdoc
     */
    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
        }
        return $this->searchCriteria;
    }

    /**
     * @inheritdoc
     */
    abstract public function getSearchResult();
}
