<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


declare(strict_types=1);

namespace Amasty\Fpc\Ui\Component\Listing;

use Amasty\Base\Model\Serializer;
use Amasty\Fpc\Api\Data\FlushesLogInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Details extends Column
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Serializer $serializer,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->serializer = $serializer;
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item[FlushesLogInterface::DETAILS] =
                $this->serializer->unserialize($item[FlushesLogInterface::DETAILS]);
        }
        return $dataSource;
    }
}
