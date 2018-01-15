<?php
namespace Aheadworks\Rma\Ui\Component\Listing\Columns;

/**
 * Interceptor class for @see \Aheadworks\Rma\Ui\Component\Listing\Columns
 */
class Interceptor extends \Aheadworks\Rma\Ui\Component\Listing\Columns implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\UiComponent\ContextInterface $context, \Magento\Framework\View\Element\UiComponentFactory $componentFactory, \Aheadworks\Rma\Api\CustomFieldRepositoryInterface $customFieldRepository, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Aheadworks\Rma\Model\CustomField\Renderer\Backend\Grid\Mapper $mapper, array $components = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($context, $componentFactory, $customFieldRepository, $searchCriteriaBuilder, $mapper, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'prepare');
        if (!$pluginInfo) {
            return parent::prepare();
        } else {
            return $this->___callPlugins('prepare', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'prepareDataSource');
        if (!$pluginInfo) {
            return parent::prepareDataSource($dataSource);
        } else {
            return $this->___callPlugins('prepareDataSource', func_get_args(), $pluginInfo);
        }
    }
}
