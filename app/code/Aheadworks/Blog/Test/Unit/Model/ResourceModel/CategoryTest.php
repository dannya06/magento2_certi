<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\Category
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category
     */
    private $categoryResourceModel;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->categoryResourceModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\ResourceModel\Category'
        );
    }

    /**
     * Testing return value of 'getValidationRulesBeforeSave' method
     */
    public function testGetValidationRulesBeforeSaveResult()
    {
        $this->assertInstanceOf(
            'Magento\Framework\Validator\DataObject',
            $this->categoryResourceModel->getValidationRulesBeforeSave()
        );
    }
}
