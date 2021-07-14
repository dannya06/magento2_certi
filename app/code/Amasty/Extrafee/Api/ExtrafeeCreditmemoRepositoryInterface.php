<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Api;

use Amasty\Extrafee\Api\Data\ExtrafeeCreditmemoInterface;

interface ExtrafeeCreditmemoRepositoryInterface
{
    /**
     * Save
     *
     * @param ExtrafeeCreditmemoInterface $creditmemoFee
     *
     * @return ExtrafeeCreditmemoInterface
     */
    public function save(ExtrafeeCreditmemoInterface $creditmemoFee): ExtrafeeCreditmemoInterface;

    /**
     * Get by id
     *
     * @param int $entityId
     *
     * @return ExtrafeeCreditmemoInterface
     */
    public function getById(int $entityId): ExtrafeeCreditmemoInterface;

    /**
     * Delete
     *
     * @param ExtrafeeCreditmemoInterface $creditmemoFee
     *
     * @return bool true on success
     */
    public function delete(ExtrafeeCreditmemoInterface $creditmemoFee): bool;
}
