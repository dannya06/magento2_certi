<?php
namespace Aheadworks\Blog\Test\Unit\Model\ResourceModel;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Model\ResourceModel\Post
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Post
     */
    private $postResourceModel;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->postResourceModel = $objectManager->getObject(
            'Aheadworks\Blog\Model\ResourceModel\Post'
        );
    }

    /**
     * Testing return value of 'getValidationRulesBeforeSave' method
     */
    public function testGetValidationRulesBeforeSaveResult()
    {
        $this->assertInstanceOf(
            'Magento\Framework\Validator\DataObject',
            $this->postResourceModel->getValidationRulesBeforeSave()
        );
    }
}
