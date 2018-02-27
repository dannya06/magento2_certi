<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rma\Test\Unit\Model\Source\Request;

use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Rma\Model\Source\Request\Status;

/**
 * Class StatusTest
 * Test for \Aheadworks\Rma\Model\Source\Request\Status
 *
 * @package Aheadworks\Rma\Test\Unit\Model\Source\Request
 */
class StatusTest extends TestCase
{
    /**
     * @var Status
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            Status::class,
            []
        );
    }

    /**
     * Test getOptionsWithoutTranslation method
     */
    public function testGetOptionsWithoutTranslation()
    {
        $this->assertTrue(is_array($this->model->getOptionsWithoutTranslation()));
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->model->toOptionArray()));
    }

    /**
     * Test getOptionLabelByValue method on unknown status
     */
    public function testGetOptionLabelByValueOnUnknown()
    {
        $value = 'unknown';

        $this->assertEmpty($this->model->getOptionLabelByValue($value));
    }

    /**
     * Test getOptionLabelByValue method
     */
    public function testGetOptionLabelByValue()
    {
        $value = Status::APPROVED;

        $this->assertInstanceOf(Phrase::class, $this->model->getOptionLabelByValue($value));
    }
}
