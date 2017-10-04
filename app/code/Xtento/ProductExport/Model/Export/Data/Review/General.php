<?php

/**
 * Product:       Xtento_ProductExport (2.3.9)
 * ID:            cb9PRAWlxmJOwg/jsj5X3dDv0+dPZORkauC/n26ZNAU=
 * Packaged:      2017-10-04T08:29:55+00:00
 * Last Modified: 2017-05-24T11:44:42+00:00
 * File:          app/code/Xtento/ProductExport/Model/Export/Data/Review/General.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Model\Export\Data\Review;

use Magento\Catalog\Api\ProductRepositoryInterface;

class General extends \Xtento\ProductExport\Model\Export\Data\Product\General
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    protected $reviewCollectionFactory;

    /**
     * General constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Xtento\XtCore\Helper\Date $dateHelper
     * @param \Xtento\XtCore\Helper\Utils $utilsHelper
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceProduct
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Xtento\XtCore\Helper\Date $dateHelper,
        \Xtento\XtCore\Helper\Utils $utilsHelper,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        ProductRepositoryInterface $productRepository,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $dateHelper, $utilsHelper, $taxConfig, $resourceProduct, $storeManager, $attributeSetFactory, $localeDate, $productRepository, $taxCalculation, $productMetadata, $objectManager, $resource, $resourceCollection, $data);

        $this->url = $urlBuilder;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
    }

    public function getConfiguration()
    {
        return [
            'name' => 'General review information',
            'category' => 'Review',
            'description' => 'Export extended review information.',
            'enabled' => true,
            'apply_to' => [\Xtento\ProductExport\Model\Export::ENTITY_REVIEW],
        ];
    }

    // @codingStandardsIgnoreStart
    public function getExportData($entityType, $collectionItem)
    {
        // @codingStandardsIgnoreEnd
        // Set return array
        $returnArray = [];
        $this->writeArray = & $returnArray; // Write directly on review level
        // Fetch fields to export
        $review = $collectionItem->getReview();

        // Timestamps of creation/update
        if ($this->fieldLoadingRequired('created_at_timestamp')) $this->writeValue('created_at_timestamp', $this->dateHelper->convertDateToStoreTimestamp($review->getCreatedAt()));

        // Which line is this?
        $this->writeValue('line_number', $collectionItem->currItemNo);
        $this->writeValue('count', $collectionItem->collectionSize);

        // Export information
        $this->writeValue('export_id', $this->_registry->registry('productexport_log') ? $this->_registry->registry('productexport_log')->getId() : 0);

        foreach ($review->getData() as $key => $value) {
            if ($key == 'entity_id') {
                continue;
            }
            if (!$this->fieldLoadingRequired($key)) {
                continue;
            }
            $this->writeValue($key, $value);
        }

        // Add rating
        if ($this->fieldLoadingRequired('product_rating')) {
            $voteValues = [];
            foreach ($review->getRatingVotes() as $vote) {
                $voteValues[] = $vote->getValue();
            }

            $averageRating = 0;
            if (count($voteValues) > 0) {
                $averageRating = array_sum($voteValues) / count($voteValues);
            }
            $this->writeValue('product_rating', $averageRating);
        }

        // Review link
        if ($this->fieldLoadingRequired('review_link')) {
            $reviewLink = $this->url->getUrl(
                'review/product/view', [
                'id' => $review->getReviewId(),
                '_store' => $this->getStoreId(),
                '_nosid' => true
            ]
            );
            $this->writeValue('review_link', $reviewLink);
        }

        // Total reviews
        if ($this->fieldLoadingRequired('total_reviews')) {
            $collection = $this->reviewCollectionFactory->create()
                ->addFieldToFilter('main_table.entity_pk_value', $review->getEntityPkValue())
                ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED);
            if ($this->getStoreId()) {
                $collection->addStoreFilter($this->getStoreId());
            }
            $this->writeValue('total_reviews', $collection->count());
        }

        $originalWriteArray = & $this->writeArray;
        // Add product information
        $productId = $review->getEntityPkValue();
        if ($productId > 0) {
            try {
                $product = $this->productRepository->getById($productId);
                if ($product->getId()) {
                    if ($this->getStoreId()) {
                        $product->setStoreId($this->getStoreId());
                    }
                    $this->writeArray = & $returnArray['product'];
                    $this->exportProductData($product, $this->writeArray);
                    $this->writeValue('entity_id', $product->getId());
                    $this->writeArray = & $originalWriteArray;
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {}
        }


        // Done
        return $returnArray;
    }
}