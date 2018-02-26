<?php

/**
 * Product:       Xtento_TrackingImport (2.3.6)
 * ID:            udfo4pHNxuS90BZUogqDpS6w1nZogQNAsyJKdEZfzKQ=
 * Packaged:      2018-02-26T09:10:55+00:00
 * Last Modified: 2017-11-21T20:06:10+00:00
 * File:          app/code/Xtento/TrackingImport/Setup/UpgradeData.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Setup;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Xtento\XtCore\Helper\Utils
     */
    protected $utilsHelper;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Xtento\XtCore\Helper\Utils $utilsHelper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Xtento\XtCore\Helper\Utils $utilsHelper
    ) {
        $this->objectManager = $objectManager;
        $this->utilsHelper = $utilsHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        if (version_compare($this->utilsHelper->getMagentoVersion(), '2.2', '>=')) {
            $this->convertSerializedDataToJson($setup);
        }
    }

    /**
     * Convert data from serialized to JSON format
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     *
     * @return void
     */
    private function convertSerializedDataToJson(\Magento\Framework\Setup\ModuleDataSetupInterface $setup)
    {
        $fieldsToConvert = ['configuration', 'conditions_serialized'];
        foreach ($fieldsToConvert as $fieldName) {
            /** @var \Magento\Framework\DB\FieldDataConverterFactory $fieldDataConverterFactory */
            $fieldDataConverterFactory = $this->objectManager->create('\Magento\Framework\DB\FieldDataConverterFactory');
            /** @var \Magento\Framework\DB\FieldDataConverter $fieldDataConverter */
            $fieldDataConverter = $fieldDataConverterFactory->create('\Xtento\TrackingImport\Test\SerializedToJsonDataConverter');
            $fieldDataConverter->convert(
                $setup->getConnection(),
                $setup->getTable('xtento_trackingimport_profile'),
                'profile_id',
                $fieldName
            );
        }
    }
}