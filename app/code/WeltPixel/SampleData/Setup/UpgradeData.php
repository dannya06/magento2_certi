<?php
namespace WeltPixel\SampleData\Setup;

use Magento\Framework\Setup;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\BlockFactory;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\Cms\Api\PageRepositoryInterface;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Setup\SampleData\Executor
     */
    protected $executor;

    /**
     * @var Updater
     */
    protected $updater;

    /**
     * @var \WeltPixel\SampleData\Model\Owl
     */
    protected $owl;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var UrlRewriteCollectionFactory
     */
    protected $urlRewriteCollectionFactory;

    /**
     * UpgradeData constructor.
     * @param Setup\SampleData\Executor $executor
     * @param Updater $updater
     * @param \WeltPixel\SampleData\Model\Owl $owl
     * @param PageFactory $pageFactory
     * @param BlockFactory $blockFactory
     * @param PageRepositoryInterface $pageRepository
     * @param UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     */
    public function __construct(
        Setup\SampleData\Executor $executor,
        Updater $updater,
        \WeltPixel\SampleData\Model\Owl $owl,
        PageFactory $pageFactory,
        BlockFactory $blockFactory,
        PageRepositoryInterface $pageRepository,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory
    )
    {
        $this->executor = $executor;
        $this->updater = $updater;
        $this->owl = $owl;
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $blockFactory;
        $this->pageRepository = $pageRepository;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $this->updater->setBlocksToCreate('WeltPixel_SampleData::fixtures/blocks/blocks_1.1.1.csv');
            $this->updater->setPagesToCreate('WeltPixel_SampleData::fixtures/pages/pages_1.1.1.csv');
            $this->executor->exec($this->updater);
        }

        /** Home page V8 */
        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            $sliderIds = $this->owl->update('1.1.2');
            $this->updater->setPagesToCreate('WeltPixel_SampleData::fixtures/pages/pages_1.1.2.csv', $sliderIds);
            $this->executor->exec($this->updater);
        }

        /** Remove the old weltpixel-icons page, new one will be added instead of that */
        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $page = $this->pageFactory->create();
            $pageCollection = $page->getCollection()->addFieldToFilter('identifier', 'weltpixel-icons');

            if ($pageCollection->getSize()) {
                $page =  $pageCollection->getFirstItem();
                $this->pageRepository->delete($page);

                $urlCollection = $this->urlRewriteCollectionFactory->create();
                $urlCollection->addFieldToFilter('request_path', 'weltpixel-icons');

                foreach ($urlCollection->getItems() as $item) {
                    $item->delete();
                }
            }
        }

        /** Custom sample pages, demo of included features */
        if (version_compare($context->getVersion(), '1.1.4', '<')) {
            $this->updater->setPagesToCreate('WeltPixel_SampleData::fixtures/pages/pages_1.1.4.csv');
            $this->executor->exec($this->updater);
        }

        /** Home page V6 */
        if (version_compare($context->getVersion(), '1.1.5', '<')) {
            $this->updater->setBlocksToCreate('WeltPixel_SampleData::fixtures/blocks/blocks_1.1.5.csv');
            $this->updater->setPagesToCreate('WeltPixel_SampleData::fixtures/pages/pages_1.1.5.csv');
            $this->executor->exec($this->updater);
        }

        /** Home page V7 */
        if (version_compare($context->getVersion(), '1.1.6', '<')) {
            $sliderIds = $this->owl->update('1.1.6');
            $this->updater->setPagesToCreate('WeltPixel_SampleData::fixtures/pages/pages_1.1.6.csv', $sliderIds);
            $this->executor->exec($this->updater);
        }

        /** Home page V9 */
        if (version_compare($context->getVersion(), '1.1.7', '<')) {
            $sliderIds = $this->owl->update('1.1.7');
            $this->updater->setPagesToCreate('WeltPixel_SampleData::fixtures/pages/pages_1.1.7.csv', $sliderIds);
            $this->executor->exec($this->updater);
        }

        /** New footer versions */
        if (version_compare($context->getVersion(), '1.1.8', '<')) {
            $this->updater->setBlocksToCreate('WeltPixel_SampleData::fixtures/blocks/blocks_1.1.8.csv');
            $this->executor->exec($this->updater);
        }

        /** Home page V5 */
        if (version_compare($context->getVersion(), '1.1.9', '<')) {
            $sliderIds = $this->owl->update('1.1.9');
            $this->updater->setPagesToCreate('WeltPixel_SampleData::fixtures/pages/pages_1.1.9.csv', $sliderIds);
            $this->executor->exec($this->updater);
        }

        if (version_compare($context->getVersion(), '1.1.10', '<')) {
            $block = $this->blockFactory->create();
            $blockCollection = $block->getCollection()
                ->addFieldToFilter('identifier', array(
                    array("like" => 'weltpixel_footer_%'),
                    array("like" => 'weltpixel_pre-footer')
                ));

            if ($blockCollection->getSize()) {
                foreach ($blockCollection->getItems() as $item) {
                    $item->setData('is_active', 1);
                    try {
                        $item->save();
                    } catch (\Exception $ex) {}
                }
            }
        }

        /** Home page V10 */
        if (version_compare($context->getVersion(), '1.1.11', '<')) {
            $sliderIds = $this->owl->update('1.1.11');
            $this->updater->setPagesToCreate('WeltPixel_SampleData::fixtures/pages/pages_1.1.11.csv', $sliderIds);
            $this->executor->exec($this->updater);
        }

        /** Global message promo block */
        if (version_compare($context->getVersion(), '1.1.12', '<')) {
            $this->updater->setBlocksToCreate('WeltPixel_SampleData::fixtures/blocks/blocks_1.1.12.csv');
            $this->executor->exec($this->updater);
        }

        /** Home page V11 */
        if (version_compare($context->getVersion(), '1.1.13', '<')) {
            $this->updater->setPagesToCreate('WeltPixel_SampleData::fixtures/pages/pages_1.1.13.csv');
            $this->updater->setBlocksToCreate('WeltPixel_SampleData::fixtures/blocks/blocks_1.1.13.csv');
            $this->executor->exec($this->updater);
        }

        $setup->endSetup();
    }
}
