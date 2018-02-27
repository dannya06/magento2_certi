<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Model\Request\PrintLabel;

use Aheadworks\Rma\Api\Data\RequestInterface;
use Aheadworks\Rma\Api\Data\RequestPrintLabelInterface;
use Aheadworks\Rma\Api\Data\RequestPrintLabelInterfaceFactory;
use Aheadworks\Rma\Model\Request\PrintLabel\Address\Form\AttributeMeta\AvailabilityChecker;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Api\AddressMetadataInterface;
use Aheadworks\Rma\Model\Request\Resolver\Order as OrderResolver;

/**
 * Class Resolver
 *
 * @package Aheadworks\Rma\Model\Request\PrintLabel
 */
class Resolver
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var RequestPrintLabelInterfaceFactory
     */
    private $requestPrintLabelFactory;

    /**
     * @var AddressMetadataInterface
     */
    private $addressMetadata;

    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @var OrderResolver
     */
    private $orderResolver;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param RequestPrintLabelInterfaceFactory $requestPrintLabelFactory
     * @param AddressMetadataInterface $addressMetadata
     * @param AvailabilityChecker $availabilityChecker
     * @param OrderResolver $orderResolver
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        RequestPrintLabelInterfaceFactory $requestPrintLabelFactory,
        AddressMetadataInterface $addressMetadata,
        AvailabilityChecker $availabilityChecker,
        OrderResolver $orderResolver
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->requestPrintLabelFactory = $requestPrintLabelFactory;
        $this->addressMetadata = $addressMetadata;
        $this->availabilityChecker = $availabilityChecker;
        $this->orderResolver = $orderResolver;
    }

    /**
     * Resolve Print Label data for order
     *
     * @param RequestInterface $request
     * @return RequestPrintLabelInterface
     */
    public function resolve($request)
    {
        $requestPrintLabelEntity = $this->requestPrintLabelFactory->create();
        $address = $this->orderResolver->getAddress($request->getOrderId());

        $this->dataObjectHelper->populateWithArray(
            $requestPrintLabelEntity,
            $this->prepareAddressData($address),
            RequestPrintLabelInterface::class
        );

        return $requestPrintLabelEntity;
    }

    /**
     * Prepare address data
     *
     * @param \Magento\Sales\Model\Order\Address $address
     * @return array
     */
    private function prepareAddressData($address)
    {
        $addressData = [];
        $attributes = $this->addressMetadata->getAttributes('customer_address_edit');
        foreach ($attributes as $attributesMeta) {
            if (!$this->availabilityChecker->isAvailableOnForm($attributesMeta)) {
                continue;
            }

            $value = $address->getData($attributesMeta->getAttributeCode());
            if (in_array($attributesMeta->getFrontendInput(), ['multiline', 'multiselect']) && !is_array($value)) {
                $value = [$value];
            }

            if (!$attributesMeta->isUserDefined()) {
                $addressData[$attributesMeta->getAttributeCode()] = $value;
            } else {
                $addressData[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES][] = [
                    AttributeInterface::ATTRIBUTE_CODE => $attributesMeta->getAttributeCode(),
                    AttributeInterface::VALUE => $value
                ];
            }
        }

        return $addressData;
    }
}
