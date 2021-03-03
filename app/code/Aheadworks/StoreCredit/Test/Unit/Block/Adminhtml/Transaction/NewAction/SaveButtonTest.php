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
namespace Aheadworks\StoreCredit\Test\Unit\Block\Adminhtml\Transaction\NewAction;

use Aheadworks\StoreCredit\Block\Adminhtml\Transaction\NewAction\SaveButton;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class Aheadworks\StoreCredit\Test\Unit\Block\Adminhtml\Transaction\NewAction\SaveButtonTest
 */
class SaveButtonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SaveButton
     */
    private $object;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->object = $objectManager->getObject(SaveButton::class, []);
    }

    /**
     * Test getButtonData method
     */
    public function testGetButtonDataMethod()
    {
        $expectsData = [
            'label' => __('Save Transaction'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
        $this->assertEquals($expectsData, $this->object->getButtonData());
    }
}
