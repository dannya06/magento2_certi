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
namespace Aheadworks\StoreCredit\Plugin\Model\Service;

use Aheadworks\StoreCredit\Api\CustomerStoreCreditManagementInterface;
use Magento\Sales\Model\Service\OrderService;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Aheadworks\StoreCredit\Plugin\Model\Service\OrderServicePlugin
 */
class OrderServicePlugin
{
    /**
     * @var CustomerStoreCreditManagementInterface
     */
    private $customerStoreCreditService;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CustomerStoreCreditManagementInterface $customerStoreCreditService
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CustomerStoreCreditManagementInterface $customerStoreCreditService,
        OrderRepositoryInterface $orderRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->customerStoreCreditService = $customerStoreCreditService;
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Reimburse Store Credit after cancel order
     *
     * @param OrderService $subject
     * @param \Closure $proceed
     * @param int $orderId
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCancel($subject, \Closure $proceed, $orderId)
    {
        $result = $proceed($orderId);
        if ($result) {
            $order = $this->orderRepository->get($orderId);
            if ($order->getCustomerId() && $order->getAwUseStoreCredit()) {
                $websiteId = $this->storeManager->getStore($order->getStoreId())->getWebsiteId();
                $this->customerStoreCreditService->reimbursedSpentStoreCreditOrderCancel(
                    $order->getCustomerId(),
                    abs($order->getBaseAwStoreCreditAmount()),
                    $order,
                    $websiteId
                );
            }
        }
        return $result;
    }

    /**
     * Spend customer Store Credit on checkout after place order
     *
     * @param  OrderService $subject
     * @param  OrderInterface $result
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(OrderService $subject, OrderInterface $result)
    {
        if ($result->getCustomerId() && $result->getAwUseStoreCredit()) {
            $websiteId = $this->storeManager->getStore($result->getStoreId())->getWebsiteId();
            $this->customerStoreCreditService->spendStoreCreditOnCheckout(
                $result->getCustomerId(),
                $result->getBaseAwStoreCreditAmount(),
                $result,
                $websiteId
            );
        }
        return $result;
    }
}
