<?php
namespace Aheadworks\AdvancedReports\Ui\DataProvider\Location\Region\DataProvider;

/**
 * Interceptor class for @see \Aheadworks\AdvancedReports\Ui\DataProvider\Location\Region\DataProvider
 */
class Interceptor extends \Aheadworks\AdvancedReports\Ui\DataProvider\Location\Region\DataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $reporting, \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\Api\FilterBuilder $filterBuilder, \Magento\Framework\Locale\FormatInterface $localeFormat, \Aheadworks\AdvancedReports\Model\Filter\Store $storeFilter, \Aheadworks\AdvancedReports\Model\Filter\Period $periodFilter, \Aheadworks\AdvancedReports\Model\Period $periodModel, \Aheadworks\AdvancedReports\Model\Config $config, array $meta = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $localeFormat, $storeFilter, $periodFilter, $periodModel, $config, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addFilter');
        if (!$pluginInfo) {
            return parent::addFilter($filter);
        } else {
            return $this->___callPlugins('addFilter', func_get_args(), $pluginInfo);
        }
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

    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSearchResult');
        if (!$pluginInfo) {
            return parent::getSearchResult();
        } else {
            return $this->___callPlugins('getSearchResult', func_get_args(), $pluginInfo);
        }
    }
}
