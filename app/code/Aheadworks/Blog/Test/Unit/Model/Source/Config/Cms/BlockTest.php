<?php
namespace Aheadworks\Blog\Test\Unit\Model\Source\Config\Cms;

use Aheadworks\Blog\Model\Source\Config\Cms\Block as CmsBlockSource;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Source\Config\Cms\Block
 */
class BlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\Source\Config\Cms\Block
     */
    private $cmsBlockSourceModel;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blockCollection;

    /**
     * @var array
     */
    private $optionArray = [['option value' => 'option label']];

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->blockCollection = $this->getMockBuilder('Magento\Cms\Model\ResourceModel\Block\Collection')
            ->setMethods(['toOptionArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $blockCollectionFactoryStub = $this->getMockBuilder('Magento\Cms\Model\ResourceModel\Block\CollectionFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $blockCollectionFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->blockCollection));
        $this->cmsBlockSourceModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\Source\Config\Cms\Block',
            ['blockCollectionFactory' => $blockCollectionFactoryStub]
        );
    }

    /**
     * Testing 'toOptionArray' method call
     */
    public function testToOptionArray()
    {
        $this->blockCollection->expects($this->atLeastOnce())
            ->method('toOptionArray')
            ->willReturn($this->optionArray);
        $this->assertEquals(
            array_merge(
                [CmsBlockSource::DONT_DISPLAY => CmsBlockSource::DONT_DISPLAY_LABEL],
                $this->optionArray
            ),
            $this->cmsBlockSourceModel->toOptionArray()
        );
    }
}
