<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Api;

use Amasty\Extrafee\Api\Data\ExtrafeeOrderInterface;

interface ExtrafeeOrderRepositoryInterface
{
    /**
     * Save
     *
     * @param ExtrafeeOrderInterface $orderFee
     *
     * @return ExtrafeeOrderInterface
     */
    public function save(ExtrafeeOrderInterface $orderFee): ExtrafeeOrderInterface;

    /**
     * Get by id
     *
     * @param int $entityId
     *
     * @return ExtrafeeOrderInterface
     */
    public function getById(int $entityId): ExtrafeeOrderInterface;

    /**
     * Delete
     *
     * @param ExtrafeeOrderInterface $orderFee
     *
     * @return bool true on success
     */
    public function delete(ExtrafeeOrderInterface $orderFee): bool;

    /**
     * Get list of fees' labels for orders. [order_id => labels]
     *
     * @param array $orderIds
     * @return array [order_id => labelsStringConcatenated]
     */
    public function getLabelsForOrders(array $orderIds): array;
}
