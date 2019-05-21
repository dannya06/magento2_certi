<?php
namespace NS8\CSP\Ui\Component\Listing\Column;

use NS8\CSP\Helper\Config;
use NS8\CSP\Helper\Logger;
use NS8\CSP\Helper\Order;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;

class NS8Status extends Column
{
    private $configHelper;
    private $logger;
    private $orderHelper;
    private $actionUrlBuilder;
    private $urlBuilder;

    public function __construct(
        Config $configHelper,
        Logger $logger,
        Order $orderHelper,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        UrlBuilder $actionUrlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->logger = $logger;
        $this->configHelper = $configHelper;
        $this->orderHelper = $orderHelper;
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');

                if (isset($item['entity_id'])) {
                    if (isset($item['ns8_status'])) {
                        $url = $this->configHelper->getAdminUrl(
                            "sales/order/view",
                            [ "active_tab" => "ns8_order_review", "order_id" => $item['entity_id'] ]
                        );
                        $ns8Status = $item['ns8_status'];

                        switch ($ns8Status) {
                            case 'approved':
                            case 'canceled':
                                $item[$name] =
                                    '<a title="View details" href="'.$url.'">'
                                    .'<div class="ns8-order-grid-cell ns8-'.$ns8Status.'-badge">'
                                    .' <div class="ns8-order-grid-resolved ">'.$ns8Status.'</div>'
                                    .'</div>'
                                    .'</a>';
                                break;
                            default:
                                $item[$name] =
                                    '<a title="View details" href="'.$url.'">'
                                    .'<div class="ns8-order-grid-cell ns8-'.$ns8Status.'-badge">'
                                    .' <div class="ns8-order-grid-score">'.$item['eq8_score'].'</div>'
                                    .' <div class="ns8-order-grid-icon"><span class="ns8-'.$ns8Status.'-icon"></div>'
                                    .'</div>'
                                    .'</a>';
                        }
                    }
                }
            }
        }
        return $dataSource;
    }
}
