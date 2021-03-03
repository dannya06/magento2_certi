<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    StoreCredit
 * @version    1.1.7
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\StoreCredit\Model\ResourceModel\Transaction\Relation\Summary;

use Aheadworks\StoreCredit\Model\Service\SummaryService;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class Aheadworks\StoreCredit\Model\ResourceModel\Transaction\Relation\Summary\SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var SummaryService
     */
    private $summaryService;

    /**
     * @param SummaryService $summaryService
     */
    public function __construct(
        SummaryService $summaryService
    ) {
        $this->summaryService = $summaryService;
    }

    /**
     *  {@inheritDoc}
     */
    public function execute($entity, $arguments = [])
    {
        $this->summaryService->addSummaryToCustomer($entity);
        return $entity;
    }
}
