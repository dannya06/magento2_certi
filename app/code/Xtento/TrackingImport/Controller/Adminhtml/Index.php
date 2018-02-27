<?php

/**
 * Product:       Xtento_TrackingImport (2.3.6)
 * ID:            udfo4pHNxuS90BZUogqDpS6w1nZogQNAsyJKdEZfzKQ=
 * Packaged:      2018-02-26T09:10:55+00:00
 * Last Modified: 2016-04-28T15:48:10+00:00
 * File:          app/code/Xtento/TrackingImport/Controller/Adminhtml/Index.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Controller\Adminhtml;

abstract class Index extends \Xtento\TrackingImport\Controller\Adminhtml\Action
{
    /**
     * Check if user has enough privileges
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * @param $resultPage \Magento\Backend\Model\View\Result\Page
     */
    protected function updateMenu($resultPage)
    {
        $resultPage->setActiveMenu('Xtento_TrackingImport::profiles');
        $resultPage->addBreadcrumb(__('Sales'), __('Sales'));
        $resultPage->addBreadcrumb(__('Tracking Import'), __('Tracking Import'));
        $resultPage->getConfig()->getTitle()->prepend(__('Tracking Import'));
    }
}
