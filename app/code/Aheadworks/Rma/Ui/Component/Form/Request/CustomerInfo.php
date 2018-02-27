<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Ui\Component\Form\Request;

use Aheadworks\Rma\Api\Data\RequestInterface;
use Aheadworks\Rma\Api\RequestRepositoryInterface;
use Aheadworks\Rma\Model\Request\Resolver\Customer as CustomerResolver;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Aheadworks\Rma\Ui\Component\Form\Request\CustomerInfo\AddressRenderer;
use Aheadworks\Rma\Model\ResourceModel\Customer\OrderTotals as CustomerOrderTotals;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class CustomerInfo
 *
 * @package Aheadworks\Rma\Ui\Component\Form\Request
 */
class CustomerInfo extends Field
{
    /**
     * @var CustomerResolver
     */
    private $customerResolver;

    /**
     * @var RequestRepositoryInterface
     */
    private $requestRepository;

    /**
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * @var CustomerOrderTotals
     */
    private $customerOrderTotals;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CustomerResolver $customerResolver
     * @param RequestRepositoryInterface $requestRepository
     * @param AddressRenderer $addressRenderer
     * @param CustomerOrderTotals $customerOrderTotals
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface $storeManager
     * @param UiComponentInterface[] $components
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CustomerResolver $customerResolver,
        RequestRepositoryInterface $requestRepository,
        AddressRenderer $addressRenderer,
        CustomerOrderTotals $customerOrderTotals,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->customerResolver = $customerResolver;
        $this->requestRepository = $requestRepository;
        $this->addressRenderer = $addressRenderer;
        $this->customerOrderTotals = $customerOrderTotals;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if ((!$this->storeManager->isSingleStoreMode() || count($this->storeManager->getWebsites()) > 1)
            && $config['dataScope'] == 'customer_previous_orders'
        ) {
            $config['label'] = __('Previous Orders (%1)', $this->storeManager->getWebsite()->getName());
        }
        $this->setData('config', $config);

        parent::prepare();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function prepareDataSource(array $dataSource)
    {
        parent::prepareDataSource($dataSource);
        $config = $this->getData('config');
        if (isset($dataSource['data']['order_id']) && $dataSource['data']['order_id']) {
            $request = $this->requestRepository->get($dataSource['data']['id']);
            $dataScope = $config['dataScope'];
            switch ($dataScope) {
                case 'customer_name':
                    if ($customerId = $this->customerResolver->getCustomerId($request)) {
                        $dataSource['data'][$dataScope . '_url'] = $this->getUrl(
                            'customer/index/edit',
                            ['id' => $customerId]
                        );
                    }
                    $dataSource['data'][$dataScope . '_label'] = $this->customerResolver->getName($request);
                    break;
                case 'customer_email':
                    $dataSource['data'][$dataScope] = $this->customerResolver->getEmail($request);
                    break;
                case 'customer_address':
                    $dataSource['data'][$dataScope] = $this->getAddress($request);
                    break;
                case 'customer_group':
                    $dataSource['data'][$dataScope] = $this->customerResolver->getGroup($request);
                    break;
                case 'customer_since':
                    $dataSource['data'][$dataScope] =
                        $this->customerResolver->getCreatedAt($request, null, \IntlDateFormatter::LONG);
                    break;
                case 'customer_previous_orders':
                    $dataSource['data'][$dataScope] = $this->getPreviousOrderDetails($request);
                    break;
            }
        }
        return $dataSource;
    }

    /**
     * Retrieve previous order details
     *
     * @param RequestInterface $request
     * @return string
     */
    private function getPreviousOrderDetails($request)
    {
        $customerId = $this->customerResolver->getCustomerId($request);
        if (empty($customerId)) {
            $customerEmail = $this->customerResolver->getEmail($request);
            $totalPurchasedAmount = $this->customerOrderTotals->getTotalPurchasedAmountByEmail(
                $customerEmail,
                $request->getStoreId()
            );
            $totalOrders = $this->customerOrderTotals->getTotalOrdersByEmail(
                $customerEmail,
                $request->getStoreId()
            );
        } else {
            $totalPurchasedAmount = $this->customerOrderTotals->getTotalPurchasedAmountById(
                $customerId,
                $request->getStoreId()
            );
            $totalOrders = $this->customerOrderTotals->getTotalOrdersById(
                $customerId,
                $request->getStoreId()
            );
        }

        $total = $this->priceCurrency->convertAndFormat(
            $totalPurchasedAmount,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $request->getStoreId()
        );

        return sprintf('%s (%s)', $totalOrders, $total);
    }

    /**
     * Retrieve customer address
     *
     * @param RequestInterface $request
     * @return string
     */
    private function getAddress($request)
    {
        $address = $this->customerResolver->getAddress($request);
        if (!empty($address)) {
            return $this->addressRenderer->render($address);
        }

        return '';
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    private function getUrl($route = '', $params = [])
    {
        return $this->getContext()->getUrl($route, $params);
    }
}
