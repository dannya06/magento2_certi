<?php
/**
 * Copyright © 2018 Icube. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icube\UpgradeScript\Setup;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var BlockFactory
     */
    protected $blockFactory;
    /**
     * Init
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory,
        PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $modelBlockFactory;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
        }
    }
    /**
     * Create page
     *
     * @return Page
     */
    public function createPage()
    {
        return $this->pageFactory->create();
    }
    /**
     * Create block
     *
     * @return Page
     */
    public function createBlock()
    {
        return $this->blockFactory->create();
    }
}