<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Icube\Information\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\RawFactory as RawResponseFactory;

/**
 * Magento Version controller: Sets the response body to ProductName/Major.MinorVersion (Edition).
 */
class Index extends Action implements HttpGetActionInterface
{
    const DEV_PREFIX = 'dev-';

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var RawResponseFactory
     */
    private $rawFactory;

    /**
     * @param Context $context
     * @param RawResponseFactory $rawFactory
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Context $context,
        RawResponseFactory $rawFactory,
        DirectoryList $directoryList,
        ProductMetadataInterface $productMetadata
    ) {
        $this->directoryList = $directoryList;
        parent::__construct($context);
        $this->rawFactory = $rawFactory;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $rawResponse = $this->rawFactory->create();

        $composerJson = $this->directoryList->getPath(DirectoryList::ROOT) . '/composer.json';
        $composerData = json_decode(file_get_contents($composerJson), true);
        $version = $composerData['version'];

        return $rawResponse->setContents("swift / .$version. (Community)");
    }
}
