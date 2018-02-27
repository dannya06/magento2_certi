<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */

namespace Amasty\Ogrid\Ui\Component;

class Columns extends \Magento\Ui\Component\Container
{
    protected $_bookmarkManagement;
    protected $_helper;
    protected $_typeToFilter = [
        'text' => 'text',
        'select' => 'text',
        'multiselect' => 'text'
    ];

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Amasty\Ogrid\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->_bookmarkManagement = $bookmarkManagement;
        $this->_helper = $helper;
    }

    public function prepare()
    {
        $ret = parent::prepare();

        $columnsConfiguration = $this->getData('config');

        if (array_key_exists('productColsData', $columnsConfiguration)) {
            $bookmark = $this->_bookmarkManagement->getByIdentifierNamespace(
                'current',
                'sales_order_grid'
            );
            $config = $bookmark ? $bookmark->getConfig() : null;
            $bookmarksCols = [];
            if (is_array($config) && isset($config['current']) && isset($config['current']['columns'])) {
                $bookmarksCols = $config['current']['columns'];
            }

            foreach ($this->getAttributeCollection() as $attribute) {
                $inputType = $attribute->getFrontendInput();
                $columnConfig = [
                    'visible' => false,
                    'filter' =>  null,
                    'label' => $attribute->getFrontendLabel(),
                    'productAttribute' => true,
                    'frontendInput' => $inputType
                ];

                if (array_key_exists($inputType, $this->_typeToFilter)) {
                    $columnConfig['filter'] = $this->_typeToFilter[$inputType];
                }
                $columnsConfiguration['productColsData'][$attribute->getAttributeDbAlias()] = $columnConfig;
            }

            foreach ($columnsConfiguration['productColsData'] as $id => &$config) {
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
            }

            $this->setData('config', $columnsConfiguration);
        }

        return $ret;
    }

    public function getAttributeCollection()
    {
        return $this->_helper->getAttributeCollection();
    }
}
