<?php
namespace Aheadworks\Giftcard\Ui\DataProvider\Product\ListingDataProvider;

/**
 * Interceptor class for @see \Aheadworks\Giftcard\Ui\DataProvider\Product\ListingDataProvider
 */
class Interceptor extends \Aheadworks\Giftcard\Ui\DataProvider\Product\ListingDataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Aheadworks\Giftcard\Model\ResourceModel\Product\CollectionFactory $collectionFactory, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, array $addFieldStrategies = array(), array $addFilterStrategies = array(), array $meta = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $productCollectionFactory, $addFieldStrategies, $addFilterStrategies, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getData');
        if (!$pluginInfo) {
            return parent::getData();
        } else {
            return $this->___callPlugins('getData', func_get_args(), $pluginInfo);
        }
    }
}
