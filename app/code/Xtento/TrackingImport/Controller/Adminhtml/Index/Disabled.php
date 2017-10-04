<?php

/**
 * Product:       Xtento_TrackingImport (2.3.0)
 * ID:            HdWKOY0KdgGaRx+26HyONH06+SvSVZH7A2yQmSKRHJU=
 * Packaged:      2017-10-04T08:30:19+00:00
 * Last Modified: 2016-03-05T11:41:29+00:00
 * File:          app/code/Xtento/TrackingImport/Controller/Adminhtml/Index/Disabled.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Controller\Adminhtml\Index;

class Disabled extends \Xtento\TrackingImport\Controller\Adminhtml\Index
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

        $this->messageManager->addWarningMessage(
            __(
                'The extension is currently disabled. Please go to System > XTENTO Extensions > Tracking Import Configuration to enable it. After that access the module at Sales > Tracking Import again.'
            )
        );
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $this->updateMenu($resultPage);
        return $resultPage;
    }
}
