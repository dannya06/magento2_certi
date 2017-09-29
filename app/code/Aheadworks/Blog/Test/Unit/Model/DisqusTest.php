<?php
namespace Aheadworks\Blog\Test\Unit\Model;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Disqus
 */
class DisqusTest extends \PHPUnit_Framework_TestCase
{
    const FORUM_CODE = 'disqus_forum_code';

    /**
     * @var \Aheadworks\Blog\Model\Disqus
     */
    private $disqusModel;

    /**
     * @var \Aheadworks\Blog\Model\Disqus\Api|\PHPUnit_Framework_MockObject_MockObject
     */
    private $disqusApi;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $storeManagerStub = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $storeManagerStub->expects($this->any())
            ->method('getWebsites')
            ->will($this->returnValue([1, 2, 3]));

        $configStub = $this->getMockBuilder('Aheadworks\Blog\Model\Config')
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $configStub->expects($this->any())->method('getValue')
            ->with($this->equalTo(Config::XML_GENERAL_DISQUS_FORUM_CODE))
            ->will($this->returnValue(self::FORUM_CODE));

        $this->disqusApi = $this->getMockBuilder('Aheadworks\Blog\Model\Disqus\Api')
            ->setMethods(['sendRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->disqusModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\Disqus',
            [
                'storeManager' => $storeManagerStub,
                'config' => $configStub,
                'disqusApi' => $this->disqusApi
            ]
        );
    }

    /**
     * Testing return value of 'getAdminUrl' method
     */
    public function testGetAdminUrlResult()
    {
        $this->assertContains(self::FORUM_CODE, $this->disqusModel->getAdminUrl());
    }
}
