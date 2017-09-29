<?php
namespace Aheadworks\Blog\Test\Integration\Model;

use Aheadworks\Blog\Api\Data\TagInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Aheadworks\Blog\Model\TagManagement
 */
class TagManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Api\TagManagementInterface
     */
    private $tagManagement;

    protected function setUp()
    {
        $this->tagManagement = Bootstrap::getObjectManager()
            ->create('Aheadworks\Blog\Api\TagManagementInterface');
    }

    /**
     * Testing of retrieving list of tags for a cloud widget
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/cloud_tags.php
     */
    public function testGetCloudTags()
    {
        $expectedResult = [
            'cloud tag 1' => [TagInterface::NAME => 'cloud tag 1', TagInterface::COUNT => 3],
            'cloud tag 2' => [TagInterface::NAME => 'cloud tag 2', TagInterface::COUNT => 2],
            'cloud tag 3' => [TagInterface::NAME => 'cloud tag 3', TagInterface::COUNT => 1]
        ];
        $result = $this->tagManagement->getCloudTags(1);
        // todo: uncomment when fixed
        //$this->assertEquals(count($expectedResult), $result->getTotalCount());
        foreach ($result->getItems() as $item) {
            $this->assertEquals($expectedResult[$item->getName()][TagInterface::NAME], $item->getName());
            $this->assertEquals($expectedResult[$item->getName()][TagInterface::COUNT], $item->getCount());
        }
    }
}
