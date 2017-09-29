<?php
namespace Aheadworks\Blog\Test\Unit\Controller\Index;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Controller\Index\Index
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    const TAG_NAME = 'tag';
    const BLOG_TITLE_CONFIG_VALUE = 'Blog';
    const META_DESCRIPTION_CONFIG_VALUE = 'Meta description';
    const ERROR_MESSAGE = 'Not found.';
    const REFERER_URL = 'http://localhost';

    /**
     * @var \Aheadworks\Blog\Controller\Index\Index
     */
    private $action;

    /**
     * @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPage;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirect;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pageConfig;

    /**
     * @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    private $title;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManager;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Aheadworks\Blog\Api\TagRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tagRepository;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tag;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->title = $this->getMock('Magento\Framework\View\Page\Title', ['set'], [], '', false);
        $this->pageConfig = $this->getMock(
            'Magento\Framework\View\Page\Config',
            ['getTitle', 'setMetadata'],
            [],
            '',
            false
        );
        $this->pageConfig->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue($this->title));
        $this->resultPage = $this->getMock('Magento\Framework\View\Result\Page', ['getConfig'], [], '', false);
        $this->resultPage->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($this->pageConfig));
        $resultPageFactoryStub = $this->getMock(
            'Magento\Framework\View\Result\PageFactory',
            ['create'],
            [],
            '',
            false
        );
        $resultPageFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->resultPage));

        $configStub = $this->getMock('Aheadworks\Blog\Model\Config', ['getValue'], [], '', false);
        $configStub->expects($this->any())
            ->method('getValue')
            ->will(
                $this->returnValueMap(
                    [
                        [Config::XML_GENERAL_BLOG_TITLE, null, null, self::BLOG_TITLE_CONFIG_VALUE],
                        [Config::XML_SEO_META_DESCRIPTION, null, null, self::META_DESCRIPTION_CONFIG_VALUE]
                    ]
                )
            );

        $this->tag = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\TagInterface');
        $this->tag->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(self::TAG_NAME));
        $this->tagRepository = $this->getMockForAbstractClass('Aheadworks\Blog\Api\TagRepositoryInterface');
        $this->tagRepository->expects($this->any())
            ->method('getByName')
            ->with($this->equalTo(self::TAG_NAME))
            ->will($this->returnValue($this->tag));

        $this->resultRedirect = $this->getMock(
            'Magento\Framework\Controller\Result\Redirect',
            ['setUrl'],
            [],
            '',
            false
        );
        $resultRedirectFactoryStub = $this->getMock(
            'Magento\Framework\Controller\Result\RedirectFactory',
            ['create'],
            [],
            '',
            false
        );
        $resultRedirectFactoryStub->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->resultRedirect));

        $this->request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $redirectStub = $this->getMockForAbstractClass('Magento\Framework\App\Response\RedirectInterface');
        $redirectStub->expects($this->any())
            ->method('getRefererUrl')
            ->will($this->returnValue(self::REFERER_URL));
        $this->messageManager = $this->getMockForAbstractClass('Magento\Framework\Message\ManagerInterface');
        $context = $objectManager->getObject(
            'Magento\Framework\App\Action\Context',
            [
                'request' => $this->request,
                'redirect' => $redirectStub,
                'messageManager' => $this->messageManager,
                'resultRedirectFactory' => $resultRedirectFactoryStub
            ]
        );

        $this->action = $objectManager->getObject(
            'Aheadworks\Blog\Controller\Index\Index',
            [
                'context' => $context,
                'resultPageFactory' => $resultPageFactoryStub,
                'tagRepository' => $this->tagRepository,
                'config' => $configStub
            ]
        );
    }

    /**
     * Testing return value of execute method
     */
    public function testExecuteResult()
    {
        $this->assertSame($this->resultPage, $this->action->execute());
    }

    /**
     * Testing return value of execute method if tag request param is set
     */
    public function testExecuteWithTagParamResult()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('tag'))
            ->willReturn(self::TAG_NAME);
        $this->assertSame($this->resultPage, $this->action->execute());
    }

    /**
     * Testing redirect if error is occur
     */
    public function testExecuteErrorRedirect()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('tag'))
            ->willReturn(self::TAG_NAME);
        $this->tagRepository->expects($this->any())
            ->method('getByName')
            ->willThrowException(
                new \Magento\Framework\Exception\LocalizedException(__(self::ERROR_MESSAGE))
            );
        $this->assertSame($this->resultRedirect, $this->action->execute());
    }

    /**
     * Testing that error message is added if error is occur
     */
    public function testExecuteErrorMessage()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('tag'))
            ->willReturn(self::TAG_NAME);
        $this->tagRepository->expects($this->any())
            ->method('getByName')
            ->willThrowException(
                new \Magento\Framework\Exception\LocalizedException(__(self::ERROR_MESSAGE))
            );
        $this->messageManager->expects($this->once())
            ->method('addError')
            ->with($this->equalTo(self::ERROR_MESSAGE));
        $this->action->execute();
    }

    /**
     * Testing that page config values is set
     */
    public function testExecutePageConfig()
    {
        $this->title->expects($this->atLeastOnce())
            ->method('set')
            ->with($this->equalTo(self::BLOG_TITLE_CONFIG_VALUE));
        $this->pageConfig->expects($this->atLeastOnce())
            ->method('setMetadata')
            ->with(
                $this->equalTo('description'),
                $this->equalTo(self::META_DESCRIPTION_CONFIG_VALUE)
            );
        $this->action->execute();
    }

    /**
     * Testing that page config values is set if tag request param is set
     */
    public function testExecutePageConfigWithTagParam()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('tag'))
            ->willReturn(self::TAG_NAME);
        $this->title->expects($this->exactly(2))
            ->method('set')
            ->withConsecutive(
                $this->anything(),
                $this->equalTo('Tagged with \'' . self::TAG_NAME . '\'')
            );
        $this->pageConfig->expects($this->atLeastOnce())
            ->method('setMetadata')
            ->with(
                $this->equalTo('description'),
                $this->equalTo(self::META_DESCRIPTION_CONFIG_VALUE)
            );
        $this->action->execute();
    }
}
