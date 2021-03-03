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
namespace Aheadworks\StoreCredit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Aheadworks\StoreCredit\Api\CustomerStoreCreditManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ProcessOrderCreate
 *
 * @package Aheadworks\StoreCredit\Observer
 */
class ProcessOrderCreate implements ObserverInterface
{
    /**
     * @var CustomerStoreCreditManagementInterface
     */
    private $customerStoreCreditService;

    /**
     * @param CustomerStoreCreditManagementInterface $customerStoreCreditService
     */
    public function __construct(
        CustomerStoreCreditManagementInterface $customerStoreCreditService
    ) {
        $this->customerStoreCreditService = $customerStoreCreditService;
    }

    /**
     * Apply store credit for admin checkout
     *
     * @param Observer $observer
     * @return $this
     * @throws NoSuchEntityException No Store Credit to be used
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        $request = $observer->getEvent()->getRequest();

        if (isset($request['payment']) && isset($request['payment']['aw_use_store_credit'])) {
            $awUseStoreCredit = (bool)$request['payment']['aw_use_store_credit'];
            if ($awUseStoreCredit && (!$quote->getCustomerId()
                || !$this->customerStoreCreditService->getCustomerStoreCreditBalance($quote->getCustomerId()))
            ) {
                throw new NoSuchEntityException(__('No Store Credit to be used'));
            }

            $quote->setAwUseStoreCredit($awUseStoreCredit);
        }

        return $this;
    }
}
