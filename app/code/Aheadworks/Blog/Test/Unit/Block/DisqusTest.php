<?php
namespace Aheadworks\Blog\Test\Unit\Block;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Disqus
 */
class DisqusTest extends \PHPUnit_Framework_TestCase
{
    const FORUM_CODE = 'disqus_forum_code';

    /**
     * @var \Aheadworks\Blog\Block\Disqus
     */
    private $block;

    /**
     * @var \Aheadworks\Blog\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->config = $this->getMock('Aheadworks\Blog\Model\Config', ['getValue'], [], '', false);
        $this->block = $objectManager->getObject('Aheadworks\Blog\Block\Disqus', ['config' => $this->config]);
    }

    /**
     * Testing of commentsEnabled method
     *
     * @dataProvider commentsEnabledDataProvider
     */
    public function testCommentsEnabled($forumCode, $expectedResult)
    {
        $this->config->expects($this->any())
            ->method('getValue')
            ->with($this->equalTo(Config::XML_GENERAL_DISQUS_FORUM_CODE))
            ->will($this->returnValue($forumCode));
        $this->assertEquals($expectedResult, $this->block->commentsEnabled());
    }

    /**
     * Testing of retrieving the value of Disqus forum code
     */
    public function testGetDisqusForumCode()
    {
        $this->config->expects($this->any())
            ->method('getValue')
            ->with($this->equalTo(Config::XML_GENERAL_DISQUS_FORUM_CODE))
            ->will($this->returnValue(self::FORUM_CODE));
        $this->assertEquals(self::FORUM_CODE, $this->block->getDisqusForumCode());
    }

    /**
     * @return array
     */
    public function commentsEnabledDataProvider()
    {
        return [
            'forum code is set' => [self::FORUM_CODE, true],
            'forum code is not set' => [null, false]
        ];
    }
}
