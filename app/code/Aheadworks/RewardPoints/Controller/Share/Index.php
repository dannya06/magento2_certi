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
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Controller\Share;

use Aheadworks\RewardPoints\Api\ProductShareManagementInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;

/**
 * Class Aheadworks\RewardPoints\Controller\Share\Index
 */
class Index extends Action
{
    /**
     * Customer session model
     *
     * @var Session
     */
    private $customerSession;

    /**
     * Product share service
     *
     * @var ProductShareManagementInterface
     */
    private $productShareService;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param ProductShareManagementInterface $productShareService
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ProductShareManagementInterface $productShareService
    ) {
        $this->customerSession = $customerSession;
        $this->productShareService = $productShareService;
        parent::__construct($context);
    }

    /**
     *  {@inheritDoc}
     */
    public function execute()
    {
        $customerId = $this->customerSession->getId();
        $productId = $this->getRequest()->getParam('productId');
        $network = $this->getRequest()->getParam('network');

        if (!$this->getRequest()->isAjax() || !$customerId || !$productId) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

        if ($this->productShareService->add($customerId, $productId, $network)) {
            $result = ['result' => 'ok'];
        } else {
            $result = ['result' => 'error'];
        }

        $this->getResponse()->appendBody(json_encode($result));
    }
}
