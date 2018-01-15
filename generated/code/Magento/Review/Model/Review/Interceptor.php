<?php
namespace Magento\Review\Model\Review;

/**
 * Interceptor class for @see \Magento\Review\Model\Review
 */
class Interceptor extends \Magento\Review\Model\Review implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory $productFactory, \Magento\Review\Model\ResourceModel\Review\Status\CollectionFactory $statusFactory, \Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory $summaryFactory, \Magento\Review\Model\Review\SummaryFactory $summaryModFactory, \Magento\Review\Model\Review\Summary $reviewSummary, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\UrlInterface $urlModel, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $registry, $productFactory, $statusFactory, $summaryFactory, $summaryModFactory, $reviewSummary, $storeManager, $urlModel, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save();
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }
}
