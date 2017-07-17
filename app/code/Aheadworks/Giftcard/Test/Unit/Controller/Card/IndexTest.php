<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Controller\Card;

use Aheadworks\Giftcard\Controller\Card\Index;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;

/**
 * Class IndexTest
 * Test for \Aheadworks\Giftcard\Controller\Card\Index
 *
 * @package Aheadworks\Giftcard\Controller\Card
 */
class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Index
     */
    private $object;

    /**
     * @var PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->resultPageFactoryMock = $this->getMock(PageFactory::class, ['create'], [], '', false);

        $this->object = $objectManager->getObject(
            Index::class,
            [
                'resultPageFactory' => $this->resultPageFactoryMock,
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $titleMock = $this->getMock(Title::class, ['set'], [], '', false);
        $pageConfigMock = $this->getMock(Config::class, ['getTitle'], [], '', false);
        $resultPageMock = $this->getMock(Page::class, ['getConfig'], [], '', false);
        $resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($pageConfigMock);
        $pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultPageMock);

        $this->assertSame($resultPageMock, $this->object->execute());
    }
}
