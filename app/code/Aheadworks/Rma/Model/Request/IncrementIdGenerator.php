<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Model\Request;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class IncrementIdGenerator
 *
 * @package Aheadworks\Rma\Model\Request
 */
class IncrementIdGenerator
{
    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * Generate increment id
     *
     * @return string
     */
    public function generate()
    {
        return sprintf("%'09u", $this->getNextIncrementId());
    }

    /**
     * Retrieve next increment id
     *
     * @return int
     * @throws LocalizedException
     */
    private function getNextIncrementId()
    {
        $entityStatus = $this->connection->showTableStatus('aw_rma_request');
        if (empty($entityStatus['Auto_increment'])) {
            throw new LocalizedException(__('Cannot get autoincrement value'));
        }
        return $entityStatus['Auto_increment'];
    }
}
