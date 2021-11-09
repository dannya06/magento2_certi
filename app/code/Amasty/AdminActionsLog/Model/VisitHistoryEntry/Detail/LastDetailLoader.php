<?php

declare(strict_types=1);

namespace Amasty\AdminActionsLog\Model\VisitHistoryEntry\Detail;

use Amasty\AdminActionsLog\Model\VisitHistoryEntry\DetailLoaderInterface;
use Amasty\AdminActionsLog\Model\VisitHistoryEntry\ResourceModel\VisitHistoryDetailCollectionFactory;
use Amasty\AdminActionsLog\Model\VisitHistoryEntry\VisitHistoryDetail;

class LastDetailLoader implements DetailLoaderInterface
{
    /**
     * @var VisitHistoryDetailCollectionFactory
     */
    private $detailCollectionFactory;

    public function __construct(VisitHistoryDetailCollectionFactory $detailCollectionFactory)
    {
        $this->detailCollectionFactory = $detailCollectionFactory;
    }

    public function loadDetails(int $visitHistoryId): array
    {
        $collection = $this->detailCollectionFactory->create();
        $collection->addFieldToFilter(VisitHistoryDetail::VISIT_ID, $visitHistoryId);
        $collection->setOrder($collection->getIdFieldName(), $collection::SORT_ORDER_DESC);
        $collection->setPageSize(1);

        return $collection->getItems();
    }
}
