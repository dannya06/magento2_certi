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
 * @package    StoreCredit
 * @version    1.1.7
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\StoreCredit\Test\Unit\Model\Filters\Transaction;

use Aheadworks\StoreCredit\Model\Filters\Transaction\CustomerSelection;
use Aheadworks\StoreCredit\Api\Data\TransactionInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class Aheadworks\StoreCredit\Test\Unit\Model\Filters\Transaction\CustomerSelectionTest
 */
class CustomerSelectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CustomerSelection
     */
    private $object;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->object = $this->objectManager->getObject(CustomerSelection::class, []);
        $ref = new \ReflectionClass($this->object);
        $prop = $ref->getProperty('fieldName');
        $prop->setAccessible(true);
        $value = $prop->getValue($this->object);
        $prop->setAccessible(false);
        $this->assertTrue($value == CustomerSelection::DEFAULT_FIELD_NAME);
    }

    /**
     * Test set custom field name
     */
    public function testCustomFieldName()
    {
        $customeFieldName = 'custome_field_name';

        $object = $this->objectManager->getObject(CustomerSelection::class, ['fieldName' => $customeFieldName]);
        $ref = new \ReflectionClass($object);
        $prop = $ref->getProperty('fieldName');
        $prop->setAccessible(true);
        $value = $prop->getValue($object);
        $prop->setAccessible(false);
        $this->assertTrue($value == $customeFieldName);
    }

    /**
     * Test filter method
     *
     * @dataProvider dataProviderFilterTest
     * @param mixed $value
     * @param mixed $expected
     */
    public function testFilterMethod($value, $expected)
    {
        $this->assertTrue(json_encode($expected) == json_encode($this->object->filter($value)));
    }

    /**
     * Data provider for filter test
     *
     * @return array
     */
    public function dataProviderFilterTest()
    {
        return [
            [1, []],
            [[], []],
            [null, []],
            ['', []],
            [new \stdClass(1), []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => 1], []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => []], []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => null], []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => ''], []],
            [[CustomerSelection::DEFAULT_FIELD_NAME => new \stdClass(1)], []],
            [
                [
                    CustomerSelection::DEFAULT_FIELD_NAME =>
                    [
                        [
                            TransactionInterface::CUSTOMER_ID => 1,
                            TransactionInterface::CUSTOMER_NAME => 'Test User',
                            TransactionInterface::CUSTOMER_EMAIL => 'test@test.com',
                            TransactionInterface::WEBSITE_ID => 1,
                        ]
                    ],
                    TransactionInterface::COMMENT_TO_CUSTOMER => 'Comment To Customer',
                    TransactionInterface::COMMENT_TO_ADMIN => 'Comment To admin',
                    TransactionInterface::BALANCE => 150,
                    TransactionInterface::WEBSITE_ID => 1,
                ],
                [
                    [
                        TransactionInterface::CUSTOMER_ID => 1,
                        TransactionInterface::CUSTOMER_NAME => 'Test User',
                        TransactionInterface::CUSTOMER_EMAIL => 'test@test.com',
                        TransactionInterface::COMMENT_TO_CUSTOMER => 'Comment To Customer',
                        TransactionInterface::COMMENT_TO_ADMIN => 'Comment To admin',
                        TransactionInterface::BALANCE => 150,
                        TransactionInterface::WEBSITE_ID => 1,
                    ]
                ]
            ],
        ];
    }
}
