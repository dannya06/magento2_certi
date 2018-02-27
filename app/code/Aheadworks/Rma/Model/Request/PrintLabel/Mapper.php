<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Model\Request\PrintLabel;

use Aheadworks\Rma\Api\Data\RequestInterface;
use Aheadworks\Rma\Api\Data\RequestPrintLabelInterface;
use Magento\Framework\EntityManager\MapperInterface;

/**
 * Class Mapper
 *
 * @package Aheadworks\Rma\Model\Request\PrintLabel
 */
class Mapper implements MapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function entityToDatabase($entityType, $data)
    {
        if (is_array($data[RequestInterface::PRINT_LABEL])) {
            $data[RequestInterface::PRINT_LABEL] = serialize($data[RequestInterface::PRINT_LABEL]);
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function databaseToEntity($entityType, $data)
    {
        $data[RequestInterface::PRINT_LABEL] = unserialize($data[RequestInterface::PRINT_LABEL]);
        $street = $data[RequestInterface::PRINT_LABEL][RequestPrintLabelInterface::STREET];
        if (!is_array($street)) {
            $data[RequestInterface::PRINT_LABEL][RequestPrintLabelInterface::STREET] = [$street, ''];
        }
        return $data;
    }
}
