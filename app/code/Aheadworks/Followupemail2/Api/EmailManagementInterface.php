<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Api;

use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\Data\StatisticsInterface;
use Aheadworks\Followupemail2\Api\Data\EmailContentInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface EmailManagementInterface
 * @package Aheadworks\Followupemail2\Api
 * @api
 */
interface EmailManagementInterface
{
    /**
     * Disable the email
     *
     * @param int $emailId
     * @return EmailInterface
     * @throws NoSuchEntityException
     */
    public function disableEmail($emailId);

    /**
     * Get emails by event id
     *
     * @param int $eventId
     * @param bool $enabledOnly
     * @return EmailInterface[]
     */
    public function getEmailsByEventId($eventId, $enabledOnly = false);

    /**
     * Change status of the email (enabled -> disabled, disabled->enabled)
     * @param int $emailId
     * @return EmailInterface
     * @throws NoSuchEntityException
     */
    public function changeStatus($emailId);

    /**
     * Is email first in the chain
     *
     * @param int|null $emailId
     * @param int $eventId
     * @return boolean
     */
    public function isFirst($emailId, $eventId);

    /**
     * Get email statistics data
     *
     * @param EmailInterface $email
     * @return StatisticsInterface
     */
    public function getStatistics($email);

    /**
     * Get email content statistics data
     *
     * @param int $emailContentId
     * @return StatisticsInterface
     */
    public function getStatisticsByContentId($emailContentId);

    /**
     * Get new email position
     *
     * @param int $eventId
     * @return int
     */
    public function getNewEmailPosition($eventId);

    /**
     * Get email preview
     *
     * @param int $storeId
     * @param EmailContentInterface $emailContent
     * @return PreviewInterface
     */
    public function getPreview($storeId, $emailContent);
}
