<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Ui\Component\Listing\Column;

use Amasty\Extrafee\Api\ExtrafeeOrderRepositoryInterface;
use Amasty\Extrafee\Model\ConfigProvider;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Extrafee extends Column
{
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var ExtrafeeOrderRepositoryInterface
     */
    private $extraFeeOrderRepository;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ConfigProvider $configProvider,
        ExtrafeeOrderRepositoryInterface $extraFeeOrderRepository,
        array $components = [],
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        $this->extraFeeOrderRepository = $extraFeeOrderRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])
            || !$this->configProvider->isShowOnOrderGrid()
        ) {
            return $dataSource;
        }

        $orderIds = array_map(function ($item) {
            return $item['entity_id'];
        }, $dataSource['data']['items']);

        $orderExtraFeeLabels = $this->extraFeeOrderRepository->getLabelsForOrders($orderIds);
        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')] = $orderExtraFeeLabels[$item['entity_id']] ?? '';
        }

        return $dataSource;
    }

    /**
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $this->_data['config']['componentDisabled'] = !$this->configProvider->isShowOnOrderGrid();
    }
}
