<?php
namespace Aheadworks\Blog\Test\Unit\Model\Disqus;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\Disqus\Api
 */
class ApiTest extends \PHPUnit_Framework_TestCase
{
    const DISQUS_SECRET_KEY = 'disqus_secret_key';
    const RESOURCE_NAME = 'disqus_api_resource';

    /**
     * @var \Aheadworks\Blog\Model\Disqus\Api
     */
    private $disqusApiModel;

    /**
     * @var \Magento\Framework\HTTP\Adapter\Curl|\PHPUnit_Framework_MockObject_MockObject
     */
    private $curl;

    /**
     * @var array
     */
    private $args = ['arg_name' => 'arg_value'];

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->curl = $this->getMockBuilder('Magento\Framework\HTTP\Adapter\Curl')
            ->setMethods(['setConfig', 'write', 'read', 'close'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->curl->expects($this->any())
            ->method('write')
            ->will($this->returnValue(''));

        $curlFactoryStub = $this->getMockBuilder('Magento\Framework\HTTP\Adapter\CurlFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $curlFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->curl));

        $configStub = $this->getMockBuilder('Aheadworks\Blog\Model\Config')
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $configStub->expects($this->any())->method('getValue')
            ->with($this->equalTo(Config::XML_GENERAL_DISQUS_SECRET_KEY))
            ->will($this->returnValue(self::DISQUS_SECRET_KEY));

        $this->disqusApiModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\Disqus\Api',
            [
                'curlFactory' => $curlFactoryStub,
                'config' => $configStub
            ]
        );
    }

    /**
     * Testing that request is sent to the remote server
     */
    public function testSendRequestWrite()
    {
        $this->curl->expects($this->once())
            ->method('write')
            ->with(
                $this->anything(),
                $this->logicalAnd(
                    $this->stringContains(self::RESOURCE_NAME),
                    $this->stringContains('api_secret=' . self::DISQUS_SECRET_KEY),
                    $this->stringContains('arg_name=arg_value')
                )
            )
            ->willReturn('');
        $this->disqusApiModel->sendRequest(self::RESOURCE_NAME, $this->args);
    }

    /**
     * Testing that response is read from the server
     */
    public function testSendRequestRead()
    {
        $this->curl->expects($this->once())->method('read');
        $this->disqusApiModel->sendRequest(self::RESOURCE_NAME, $this->args);
    }

    /**
     * Testing that the connection to the server is closed
     */
    public function testSendRequestClose()
    {
        $this->curl->expects($this->once())->method('close');
        $this->disqusApiModel->sendRequest(self::RESOURCE_NAME, $this->args);
    }

    /**
     * Testing response from the server
     */
    public function testSendRequestResponse()
    {
        $response = '{"response":[{"fieldName": "fieldValue"}]}';
        $this->curl->expects($this->any())
            ->method('read')
            ->willReturn($response);
        $this->assertEquals(
            [['fieldName' => 'fieldValue']],
            $this->disqusApiModel->sendRequest(self::RESOURCE_NAME, $this->args)
        );
    }

    /**
     * Testing response from the server in the case of exception is thrown
     */
    public function testSendRequestReadException()
    {
        $this->curl->expects($this->any())
            ->method('read')
            ->willThrowException(new \Exception());
        $this->assertFalse($this->disqusApiModel->sendRequest(self::RESOURCE_NAME, $this->args));
    }
}
