<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Rbslider\Test\Unit\Block\Adminhtml\Slide\Edit\Button;

use Aheadworks\Layerednav\Block\Adminhtml\Filter\Edit\Button\SaveAndContinue as SaveAndContinueButton;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Block\Adminhtml\Filter\Edit\Button\SaveAndContinue
 */
class SaveAndContinueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SaveAndContinueButton
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
            SaveAndContinueButton::class,
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
