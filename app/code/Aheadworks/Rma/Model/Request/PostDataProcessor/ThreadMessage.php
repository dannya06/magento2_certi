<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Model\Request\PostDataProcessor;

use Aheadworks\Rma\Api\Data\RequestInterface;
use Aheadworks\Rma\Api\Data\ThreadMessageInterface;

/**
 * Class ThreadMessage
 *
 * @package Aheadworks\Rma\Model\Request\PostDataProcessor
 */
class ThreadMessage implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if ($this->isThreadMessageEmpty($data) && $this->isThreadMessageAttachmentEmpty($data)) {
            $data[RequestInterface::THREAD_MESSAGE] = null;
        }

        return $data;
    }

    /**
     * Check if thread message empty
     *
     * @param array $data
     * @return bool
     */
    private function isThreadMessageEmpty($data)
    {
        return isset($data[RequestInterface::THREAD_MESSAGE][ThreadMessageInterface::TEXT])
            && empty($data[RequestInterface::THREAD_MESSAGE][ThreadMessageInterface::TEXT]);
    }

    /**
     * Check if thread message attachment empty
     *
     * @param array $data
     * @return bool
     */
    private function isThreadMessageAttachmentEmpty($data)
    {
        return !isset($data[RequestInterface::THREAD_MESSAGE][ThreadMessageInterface::ATTACHMENTS])
            || empty($data[RequestInterface::THREAD_MESSAGE][ThreadMessageInterface::ATTACHMENTS]);
    }
}
