<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Test\Unit\Controller\Adminhtml\ProductConversion\Variant;

use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReports\Controller\Adminhtml\ProductConversion\Variant\Index;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;

/**
 * Test for \Aheadworks\AdvancedReports\Controller\Adminhtml\ProductConversion\Variant\Index
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Index
     */
    private $controller;

    /**
     * @var PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageFactoryMock;

    /**
     * @var Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultPageFactoryMock = $this->getMock(PageFactory::class, ['create'], [], '', false);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);

        $this->resultRedirectMock = $this->getMock(Redirect::class, ['setPath'], [], '', false);
        $resultRedirectFactoryMock = $this->getMock(RedirectFactory::class, ['create'], [], '', false);
        $resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultRedirectMock);

        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'resultRedirectFactory' => $resultRedirectFactoryMock
            ]
        );

        $this->controller = $objectManager->getObject(
            Index::class,
            [
                'context' => $contextMock,
                'resultPageFactory' => $this->resultPageFactoryMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $productId = 1;
        $productName = base64_encode('Product 1');

        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('product_id')
            ->willReturn($productId);
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('product_name')
            ->willReturn($productName);

        $titleMock = $this->getMock(Title::class, ['prepend'], [], '', false);
        $titleMock->expects($this->once())
            ->method('prepend')
            ->willReturnSelf();
        $pageConfigMock = $this->getMock(Config::class, ['getTitle'], [], '', false);
        $pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);
        $resultPageMock = $this->getMock(Page::class, ['setActiveMenu', 'getConfig'], [], '', false);
        $resultPageMock->expects($this->any())
            ->method('setActiveMenu')
            ->willReturnSelf();
        $resultPageMock->expects($this->any())
            ->method('getConfig')
            ->willReturn($pageConfigMock);
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultPageMock);

        $this->assertSame($resultPageMock, $this->controller->execute());
    }

    /**
     * Testing of redirection if product_id in request is not exists
     */
    public function testExecuteRedirectProductIdInRequestNotExists()
    {
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('product_id')
            ->willReturn(null);
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('product_name')
            ->willReturn(null);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/productconversion/index')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->controller->execute());
    }
}
