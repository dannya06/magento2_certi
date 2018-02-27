<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Model\Request\Email\Processor;

/**
 * Class AdminReply
 *
 * @package Aheadworks\Rma\Model\Request\Email\Processor
 */
class AdminReply extends AbstractProcessor
{
    /**
     * {@inheritdoc}
     */
    protected function prepareRequestTemplateVariables()
    {
        $requestVariables = [
            'admin_url' => $this->getAdminRmaUrl()
        ];

        return $requestVariables;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateId()
    {
        return $this->config->getEmailTemplateReplyByAdmin($this->getStoreId());
    }

    /**
     * {@inheritdoc}
     */
    protected function getRecipientName()
    {
        return $this->getSenderName();
    }

    /**
     * {@inheritdoc}
     */
    protected function getRecipientEmail()
    {
        return $this->getSenderEmail();
    }
}
