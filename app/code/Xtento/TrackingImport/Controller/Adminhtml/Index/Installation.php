<?php

/**
 * Product:       Xtento_TrackingImport (2.3.6)
 * ID:            udfo4pHNxuS90BZUogqDpS6w1nZogQNAsyJKdEZfzKQ=
 * Packaged:      2018-02-26T09:10:55+00:00
 * Last Modified: 2016-10-27T14:10:30+00:00
 * File:          app/code/Xtento/TrackingImport/Controller/Adminhtml/Index/Installation.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Controller\Adminhtml\Index;

class Installation extends \Xtento\TrackingImport\Controller\Adminhtml\Index
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $healthCheck = $this->healthCheck();
        if ($healthCheck !== true) {
            $resultRedirect = $this->resultFactory->create(
                \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
            );
            return $resultRedirect->setPath($healthCheck);
        }

        $this->messageManager->addComplexWarningMessage(
            'backendHtmlMessage',
            [
                'html' => (string)__(
                    'The extension has not been installed properly. The required database tables have not been created yet. Please check out our <a href="http://support.xtento.com/wiki/Troubleshooting:_Database_tables_have_not_been_initialized_(Magento_2)" target="_blank">wiki</a> for instructions. After following these instructions access the module at Sales > Tracking Import again.'
                )
            ]
        );
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $this->updateMenu($resultPage);
        return $resultPage;
    }
}
