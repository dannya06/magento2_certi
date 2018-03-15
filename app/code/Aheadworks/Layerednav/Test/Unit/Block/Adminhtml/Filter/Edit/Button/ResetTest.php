<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Rbslider\Test\Unit\Block\Adminhtml\Slide\Edit\Button;

use Aheadworks\Layerednav\Block\Adminhtml\Filter\Edit\Button\Reset as ResetButton;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Block\Adminhtml\Filter\Edit\Button\Reset
 */
class ResetTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResetButton
     */
    private $button;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->button = $objectManager->getObject(
            ResetButton::class,
            []
        );
    }

    /**
     * Test getButtonData method
     */
    public function testGetButtonData()
    {
        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}
