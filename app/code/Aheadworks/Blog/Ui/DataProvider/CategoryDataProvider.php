<?php
namespace Aheadworks\Blog\Ui\DataProvider;

use Aheadworks\Blog\Api\Data\CategoryInterface;
use Aheadworks\Blog\Api\CategoryRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Category data provider
 */
class CategoryDataProvider extends AbstractDataProvider
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;

    /**
     * CategoryDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CategoryRepositoryInterface $repository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObjectProcessor $dataObjectProcessor
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CategoryRepositoryInterface $repository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObjectProcessor $dataObjectProcessor,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $filterBuilder,
            $searchCriteriaBuilder,
            $dataObjectProcessor,
            $request,
            $meta,
            $data
        );
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $searchResult = $this->getSearchResult();
        $data = [];
        $data['totalRecords'] = $searchResult->getTotalCount();
        $data['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $itemData = $this->dataObjectProcessor->buildOutputDataArray(
                $item,
                'Aheadworks\Blog\Api\Data\CategoryInterface'
            );
            $itemData['store_id'] = $itemData[CategoryInterface::STORE_IDS];
            $data['items'][] = $itemData;
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getSearchResult()
    {
        return $this->repository->getList($this->getSearchCriteria());
    }
}
