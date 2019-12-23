<?php

namespace Icube\CatalogSorting\Block\Product\ProductList;

use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Select;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    const SORT_ORDER_DESC = 'DESC';
    protected $_subQueryApplied = false;

    /**
    * Constructor
    *
    * @param \Magento\Framework\App\ResourceConnection $resource
    */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Config $catalogConfig,
        ToolbarModel $toolbarModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        ProductList $productListHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = [],
        ToolbarMemorizer $toolbarMemorizer = null,
        \Magento\Framework\App\Http\Context $httpContext = null,
        \Magento\Framework\Data\Form\FormKey $formKey = null
    ) {
        $this->_catalogSession = $catalogSession;
        $this->_catalogConfig = $catalogConfig;
        $this->_toolbarModel = $toolbarModel;
        $this->urlEncoder = $urlEncoder;
        $this->_productListHelper = $productListHelper;
        $this->_postDataHelper = $postDataHelper;
        $this->toolbarMemorizer = $toolbarMemorizer ?: ObjectManager::getInstance()->get(
            ToolbarMemorizer::class
        );
        $this->httpContext = $httpContext ?: ObjectManager::getInstance()->get(
            \Magento\Framework\App\Http\Context::class
        );
        $this->formKey = $formKey ?: ObjectManager::getInstance()->get(
            \Magento\Framework\Data\Form\FormKey::class
        );
        parent::__construct($context,$catalogSession,$catalogConfig,$toolbarModel,$urlEncoder,$productListHelper,$postDataHelper,$data, $toolbarMemorizer, $httpContext, $formKey);
        $this->_conn = $resource->getConnection('catalog');
    }

    public function setCollection($collection)
    {
        if (empty($this->getRequest()->getParam('product_list_order'))) {
            $this->getRequest()->setParam('product_list_order', 'newest');
        }

        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            if ($this->getCurrentOrder() == 'most_viewed') {
                $reportEventTable = $this->_collection->getResource()->getTable('report_event');
                $subSelect = $this->_conn->select()->from(
                    ['report_event_table' => $reportEventTable],
                    'COUNT(report_event_table.event_id)'
                )->where(
                    'report_event_table.object_id = e.entity_id'
                );
                $this->_collection->getSelect()->reset(Select::ORDER)->columns(['views' => $subSelect])->order('views desc');
            } else if ($this->getCurrentOrder() == 'bestseller') {
                $this->_collection->addAttributeToSort(
                    'icube_sold',
                    'desc'
                );
            } else if ($this->getCurrentOrder() == "newest") {
                $this->_collection->setOrder('created_at', 'desc');
            } else if ($this->getCurrentOrder() == 'position') {
                $this->_collection->addAttributeToSort(
                    $this->getCurrentOrder(), 
                    $this->getCurrentDirection()
                );
            } else {
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            }
        }
        return $this;
    }

    public function getCurrentDirectionReverse() {
            if ($this->getCurrentDirection() == 'asc') {
                return 'desc';
            } elseif ($this->getCurrentDirection() == 'desc') {
                return 'asc';
            } else {
                return $this->getCurrentDirection();
            }
        }

}