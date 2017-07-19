<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */

namespace Amasty\Ogrid\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponentInterface;

class Columns extends \Magento\Ui\Component\Listing\Columns
{
    protected $_bookmarkManagement;
    protected $_componentFactory;
    protected $_helper;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement,
        \Magento\Framework\View\Element\UiComponentFactory $componentFactory,
        \Amasty\Ogrid\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->_bookmarkManagement = $bookmarkManagement;
        $this->_componentFactory = $componentFactory;
        $this->_helper = $helper;
    }

    public function prepare()
    {
        $ret = parent::prepare();

        $this->_prepareColumns();
        return $ret;
    }

    protected function _prepareColumns()
    {
        $bookmark = $this->_bookmarkManagement->getByIdentifierNamespace(
            'current',
            'sales_order_grid'
        );

        $config = $bookmark ? $bookmark->getConfig() : null;
        $bookmarksCols = [];

        if (is_array($config) && isset($config['current']) && isset($config['current']['columns'])) {
            $bookmarksCols = $config['current']['columns'];
        }

        foreach ($this->getChildComponents() as $id => $column) {
            if ($column instanceof \Magento\Ui\Component\Listing\Columns\Column) {
                $config = $column->getData('config');
                $config['amogrid'] = [
                    'label'   => $config['label'],
                    'title'   => '',
                    'visible' => true
                ];

                if (isset($bookmarksCols[$id]) && isset($bookmarksCols[$id]['amogrid_title'])) {
                    $config['amogrid']['title'] = $bookmarksCols[$id]['amogrid_title'];
                } elseif (isset($config['label'])) {
                    $config['amogrid']['title'] = $config['label'];
                }

                if (isset($bookmarksCols[$id]) && isset($bookmarksCols[$id]['visible'])) {
                    $config['amogrid']['visible'] = $bookmarksCols[$id]['visible'];
                } elseif (isset($config['visible'])) {
                    $config['amogrid']['visible'] = $config['visible'];
                }

                $config['label'] = $config['amogrid']['title'];

                $column->setData('config', $config);
            }
        }
    }
}
