<?php
namespace Aheadworks\Blog\Test\Unit\Block;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Link
 */
class LinkTest extends \PHPUnit_Framework_TestCase
{
    const LINK_URL = 'http://localhost';
    const LINK_TITLE = 'Link';
    const LINK_LABEL = 'Link';

    /**
     * @var \Aheadworks\Blog\Block\Link
     */
    private $block;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $escaperStub = $this->getMock('Magento\Framework\Escaper', ['escapeHtml'], [], '', false);
        $escaperStub->expects($this->any())
            ->method('escapeHtml')
            ->will($this->returnArgument(0));
        $contextStub = $objectManager->getObject(
            'Magento\Framework\View\Element\Template\Context',
            ['escaper' => $escaperStub]
        );

        $this->block = $objectManager->getObject(
            'Aheadworks\Blog\Block\Link',
            ['context' => $contextStub]
        );
    }

    /**
     * Testing of getHref method
     */
    public function testGetHref()
    {
        $this->block->setData('href', self::LINK_URL);
        $this->assertEquals(self::LINK_URL, $this->block->getHref());
    }

    /**
     * Testing of _toHtml method
     */
    public function testToHtml()
    {
        $this->block->setData('href', self::LINK_URL);
        $this->block->setData('title', self::LINK_TITLE);
        $this->block->setData('label', self::LINK_LABEL);
        $this->assertEquals(
            '<a href="' . self::LINK_URL . '" title="' . self::LINK_TITLE . '" >' . self::LINK_LABEL . '</a>',
            $this->block->toHtml()
        );
    }
}
