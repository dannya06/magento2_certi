<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Rma\Model\Status;

use Magento\Framework\DataObject;

/**
 * Class Restrictions
 *
 * @package Aheadworks\Rma\Model\Status
 */
class Restrictions extends DataObject implements RestrictionsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAvailableForStatuses()
    {
        return $this->getData(self::AVAILABLE_FOR_STATUSES);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableForActions()
    {
        return $this->getData(self::AVAILABLE_FOR_ACTIONS);
    }
}
