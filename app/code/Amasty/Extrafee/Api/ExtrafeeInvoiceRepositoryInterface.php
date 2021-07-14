<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


declare(strict_types=1);

namespace Amasty\Extrafee\Api;

use Amasty\Extrafee\Api\Data\ExtrafeeInvoiceInterface;

interface ExtrafeeInvoiceRepositoryInterface
{
    /**
     * Save
     *
     * @param ExtrafeeInvoiceInterface $invoiceFee
     *
     * @return ExtrafeeInvoiceInterface
     */
    public function save(ExtrafeeInvoiceInterface $invoiceFee): ExtrafeeInvoiceInterface;

    /**
     * Get by id
     *
     * @param int $entityId
     *
     * @return ExtrafeeInvoiceInterface
     */
    public function getById(int $entityId): ExtrafeeInvoiceInterface;

    /**
     * Delete
     *
     * @param ExtrafeeInvoiceInterface $invoiceFee
     *
     * @return bool true on success
     */
    public function delete(ExtrafeeInvoiceInterface $invoiceFee): bool;
}
