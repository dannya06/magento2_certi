<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Followupemail2\Api;

use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\Data\EventHistoryInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueInterface;
use Aheadworks\Followupemail2\Api\Data\QueueInterface;

/**
 * Interface EventQueueManagementInterface
 * @package Aheadworks\Followupemail2\Api
 * @api
 */
interface EventQueueManagementInterface
{
    /**
     * Cancel queued events
     *
     * @param string $eventCode
     * @param int $referenceId
     * @return bool
     */
    public function cancelEvents($eventCode, $referenceId);

    /**
     * Cancel queued events by campaign id
     *
     * @param int $campaignId
     * @return bool
     */
    public function cancelEventsByCampaignId($campaignId);

    /**
     * Cancel queued events by event id
     *
     * @param int $eventId
     * @return bool
     */
    public function cancelEventsByEventId($eventId);

    /**
     * Add new event to queue
     *
     * @param EventInterface $event
     * @param EventHistoryInterface $eventHistoryItem
     * @return EventQueueInterface|false
     */
    public function add(EventInterface $event, EventHistoryInterface $eventHistoryItem);

    /**
     * Process unprocessed event queue items
     *
     * @param int $maxItemsCount
     * @return bool
     */
    public function processUnprocessedItems($maxItemsCount);
}
