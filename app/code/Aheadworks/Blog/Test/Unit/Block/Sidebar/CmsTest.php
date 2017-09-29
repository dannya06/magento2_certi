<?php
namespace Aheadworks\Blog\Test\Unit\Block\Sidebar;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Sidebar\Cms
 */
class CmsTest extends \PHPUnit_Framework_TestCase
{
    const CMS_BLOCK_ID = 1;
    const CMS_BLOCK_CONTENT = '<p>Cms block content.</p>';
    const STORE_ID = 1;

    /**
     * @var \Aheadworks\Blog\Block\Sidebar\Cms
     */
    private $block;

    /**
     * @var \Aheadworks\Blog\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \Magento\Cms\Model\Block|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cmsBlock;

    /**
     * @var \Magento\Framework\Filter\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filter;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->config = $this->getMock('Aheadworks\Blog\Model\Config', ['getValue'], [], '', false);

        $this->cmsBlock = $this->getMock(
            'Magento\Cms\Model\Block',
            ['setStoreId', 'load', 'getContent'],
            [],
            '',
            false
        );
        $this->cmsBlock->expects($this->any())
            ->method('setStoreId')
            ->will($this->returnSelf());
        $this->cmsBlock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $cmsBlockFactoryStub = $this->getMock('Magento\Cms\Model\BlockFactory', ['create'], [], '', false);
        $cmsBlockFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->cmsBlock));

        $this->filter = $this->getMock('Magento\Framework\Filter\Template', ['setStoreId', 'filter'], [], '', false);
        $this->filter->expects($this->any())
            ->method('setStoreId')
            ->will($this->returnSelf());
        $cmsFilterProviderStub = $this->getMock(
            'Magento\Cms\Model\Template\FilterProvider',
            ['getBlockFilter'],
            [],
            '',
            false
        );
        $cmsFilterProviderStub->expects($this->any())
            ->method('getBlockFilter')
            ->will($this->returnValue($this->filter));

        $storeStub = $this->getMockForAbstractClass('Magento\Store\Api\Data\StoreInterface');
        $storeStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::STORE_ID));
        $storeManagerStub = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');
        $storeManagerStub->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeStub));
        $context = $objectManager->getObject(
            'Magento\Framework\View\Element\Template\Context',
            ['storeManager' => $storeManagerStub]
        );

        $this->block = $objectManager->getObject(
            'Aheadworks\Blog\Block\Sidebar\Cms',
            [
                'context' => $context,
                'config' => $this->config,
                'cmsBlockFactory' => $cmsBlockFactoryStub,
                'cmsFilterProvider' => $cmsFilterProviderStub
            ]
        );
    }

    /**
     * Testing of retrieving of cms block instance
     */
    public function testGetCmsBlock()
    {
        $this->config->expects($this->any())
            ->method('getValue')
            ->with($this->equalTo(Config::XML_SIDEBAR_CMS_BLOCK))
            ->willReturn(self::CMS_BLOCK_ID);
        $this->assertEquals($this->cmsBlock, $this->block->getCmsBlock());
    }

    /**
     * Testing of retrieving of cms block html
     */
    public function testGetCmsBlockHtml()
    {
        $this->cmsBlock->expects($this->any())
            ->method('getContent')
            ->willReturn(self::CMS_BLOCK_CONTENT);
        $this->filter->expects($this->atLeastOnce())
            ->method('filter')
            ->with($this->equalTo(self::CMS_BLOCK_CONTENT))
            ->willReturn(self::CMS_BLOCK_CONTENT);
        $this->assertEquals(self::CMS_BLOCK_CONTENT, $this->block->getCmsBlockHtml($this->cmsBlock));
    }
}
