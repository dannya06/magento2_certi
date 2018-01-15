<?php
namespace Magento\GroupedProduct\Ui\DataProvider\Product\GroupedProductDataProvider;

/**
 * Interceptor class for @see \Magento\GroupedProduct\Ui\DataProvider\Product\GroupedProductDataProvider
 */
class Interceptor extends \Magento\GroupedProduct\Ui\DataProvider\Product\GroupedProductDataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory, \Magento\Framework\App\RequestInterface $request, \Magento\Catalog\Model\ProductTypes\ConfigInterface $config, \Magento\Store\Api\StoreRepositoryInterface $storeRepository, array $meta = array(), array $data = array(), array $addFieldStrategies = array(), array $addFilterStrategies = array())
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $request, $config, $storeRepository, $meta, $data, $addFieldStrategies, $addFilterStrategies);
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
