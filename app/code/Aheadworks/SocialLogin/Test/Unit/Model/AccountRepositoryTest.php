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
 * @package    SocialLogin
 * @version    1.6.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\SocialLogin\Test\Unit\Model;

use Aheadworks\SocialLogin\Model\AccountRepository;
use Aheadworks\SocialLogin\Model\ResourceModel\Account as AccountResource;
use Aheadworks\SocialLogin\Model\Account;
use Aheadworks\SocialLogin\Api\Data\AccountInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class AccountRepositoryTest
 * @package Aheadworks\SocialLogin\Test\Unit\Model
 */
class AccountRepositoryTest extends TestCase
{
    /**
     * @var AccountRepository
     */
    private $model;

    /**
     * @var AccountResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(
            AccountResource::class,
            ['delete']
        );
        $this->model = $objectManager->getObject(
            AccountRepository::class,
            [
                'resource' => $this->resourceMock
            ]
        );
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        /** @var AccountInterface|\PHPUnit_Framework_MockObject_MockObject $accountMock */
        $accountMock = $this->createPartialMock(Account::class, ['getId']);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->willReturnSelf();

        $this->assertSame(true, $this->model->delete($accountMock));
    }
}

