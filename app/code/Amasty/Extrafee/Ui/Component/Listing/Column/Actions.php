<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Extrafee
 */


namespace Amasty\Extrafee\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    /** @var UrlInterface  */
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dSource
     * @return array
     */
    public function prepareDataSource(array $dSource)
    {
        if (isset($dSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'hidden' => false,
                    'label' => __('Edit'),
                    'href' => $this->urlBuilder->getUrl(
                        'amasty_extrafee/*/edit',
                        [
                            'id' => $item['entity_id'],
                            'store' => $storeId
                        ]
                    )
                ];
            }
        }

        return $dSource;
    }
}
