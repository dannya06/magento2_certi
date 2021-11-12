<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icube\Information\Block\Page;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Adminhtml footer block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Footer extends \Magento\Backend\Block\Template
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var string
     */
    protected $_template = 'Icube_Information::page/footer.phtml';
    

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     * @since 100.1.0
     */
    protected $productMetadata;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        DirectoryList $directoryList,
        array $data = []
    ) {
        $this->directoryList = $directoryList;
        $this->productMetadata = $productMetadata;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->setShowProfiler(true);
    }

    /**
     * Get product version
     *
     * @return string
     * @since 100.1.0
     */
    public function getMagentoVersion()
    {
         return $this->productMetadata->getVersion();
    }

    /**
     * Get product version
     *
     * @return string
     * @since 100.1.0
     */
    public function getSwiftVersion()
    {
        $composerJson = $this->directoryList->getPath(DirectoryList::ROOT) . '/composer.json';
        $composerData = json_decode(file_get_contents($composerJson), true);
        $version = $composerData['version'];
        return $version;
    }
    
}
