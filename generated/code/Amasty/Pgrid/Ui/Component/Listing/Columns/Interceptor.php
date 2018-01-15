<?php
namespace Amasty\Pgrid\Ui\Component\Listing\Columns;

/**
 * Interceptor class for @see \Amasty\Pgrid\Ui\Component\Listing\Columns
 */
class Interceptor extends \Amasty\Pgrid\Ui\Component\Listing\Columns implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\UiComponent\ContextInterface $context, \Amasty\Pgrid\Ui\Component\ColumnFactory $columnFactory, \Amasty\Pgrid\Ui\Component\Listing\Attribute\Repository $attributeRepository, \Amasty\Pgrid\Ui\Component\Listing\Column\InlineEditUpdater $inlineEditUpdater, \Amasty\Pgrid\Helper\Data $helper, \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement, array $components = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($context, $columnFactory, $attributeRepository, $inlineEditUpdater, $helper, $bookmarkManagement, $components, $data);
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
