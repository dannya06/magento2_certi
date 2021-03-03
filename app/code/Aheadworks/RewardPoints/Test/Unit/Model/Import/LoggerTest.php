<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    1.7.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Test\Unit\Model\Import;

use Aheadworks\RewardPoints\Model\Import\Logger;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Zend\Log\Logger as ZendLogger;

/**
 * Class LoggerTest
 * Test for \Aheadworks\RewardPoints\Model\Import\Logger
 */
class LoggerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    private $model;

    /**
     * @var ZendLogger|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->loggerMock = $this->getMockBuilder(Logger::class)
            ->setMethods(['info'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            Logger::class,
            [
                'logger' => $this->loggerMock,
            ]
        );
    }

    /**
     * Testing of addMessage method
     */
    public function testAddMessage()
    {
        $message = __('Message');

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with($message)
            ->willReturnSelf();

        $this->model->addMessage($message);
    }
}
