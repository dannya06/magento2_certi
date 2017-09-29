<?php
namespace Aheadworks\Blog\Test\Unit\Block\Widget;

use Aheadworks\Blog\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Blog\Block\Widget\TagCloud
 */
class TagCloudTest extends \PHPUnit_Framework_TestCase
{
    const MAX_WEIGHT = 1.5;
    const MIN_WEIGHT = 0.5;
    const SLOPE = 0.1;

    const SEARCH_BY_TAG_URL = 'http://localhost/blog/tag/tag';

    const STORE_ID = 1;

    /**
     * @var \Aheadworks\Blog\Block\Widget\TagCloud
     */
    private $block;

    /**
     * @var \Aheadworks\Blog\Api\Data\TagSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResults;

    /**
     * @var \Aheadworks\Blog\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \Aheadworks\Blog\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    private $url;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->searchResults = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\TagSearchResultsInterface');
        $tagManagementStub = $this->getMockForAbstractClass('Aheadworks\Blog\Api\TagManagementInterface');
        $tagManagementStub->expects($this->any())
            ->method('getCloudTags')
            ->with($this->equalTo(self::STORE_ID))
            ->will($this->returnValue($this->searchResults));

        $this->config = $this->getMock('Aheadworks\Blog\Model\Config', ['getValue'], [], '', false);
        $this->url = $this->getMock('Aheadworks\Blog\Model\Url', ['getSearchByTagUrl'], [], '', false);

        $requestStub = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $requestStub->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('blog_category_id'))
            ->will($this->returnValue(null));
        $storeStub = $this->getMockForAbstractClass('Magento\Store\Api\Data\StoreInterface');
        $storeStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::STORE_ID));
        $storeManagerStub = $this->getMockForAbstractClass('Magento\Store\Model\StoreManagerInterface');
        $storeManagerStub->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeStub));
        $context = $objectManager->getObject(
            'Magento\Framework\View\Element\Template\Context',
            [
                'request' => $requestStub,
                'storeManager' => $storeManagerStub
            ]
        );

        $this->block = $objectManager->getObject(
            'Aheadworks\Blog\Block\Widget\TagCloud',
            [
                'context' => $context,
                'tagManagement' => $tagManagementStub,
                'config' => $this->config,
                'url' => $this->url,
                'data' => [
                    'max_weight' => self::MAX_WEIGHT,
                    'min_weight' => self::MIN_WEIGHT,
                    'slope' => self::SLOPE
                ]
            ]
        );
    }

    /**
     * Testing of retrieving of tags
     */
    public function testGetTags()
    {
        $count = 1;
        $tag1 = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\TagInterface');
        $tag1->expects($this->any())
            ->method('getCount')
            ->will($this->returnValue($count));
        $tag2 = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\TagInterface');
        $tag2->expects($this->any())
            ->method('getCount')
            ->will($this->returnValue($count));
        $this->searchResults->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn([$tag1, $tag2]);
        $this->assertEquals([$tag1, $tag2], $this->block->getTags());
    }

    /**
     * testing of isCloud method
     *
     * @dataProvider isCloudDataProvider
     */
    public function testIsCloud($configValue, $expectedResult)
    {
        $this->config->expects($this->any())
            ->method('getValue')
            ->with($this->equalTo(Config::XML_SIDEBAR_HIGHLIGHT_TAGS))
            ->willReturn($configValue);
        $this->assertEquals($expectedResult, $this->block->isCloud());
    }

    /**
     * Testing of tag weight calculation
     *
     * @dataProvider getTagWeightDataProvider
     */
    public function testGetTagWeight($count, $countsAll, $weight)
    {
        $tagsAll = [];
        foreach ($countsAll as $postCounts) {
            $tag = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\TagInterface');
            $tag->expects($this->any())
                ->method('getCount')
                ->willReturn($postCounts);
            $tagsAll[] = $tag;
        }
        $this->searchResults->expects($this->any())
            ->method('getItems')
            ->willReturn($tagsAll);
        $this->block->getTags();
        /** @var \Aheadworks\Blog\Api\Data\TagInterface|\PHPUnit_Framework_MockObject_MockObject $tag */
        $tag = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\TagInterface');
        $tag->expects($this->any())
            ->method('getCount')
            ->willReturn($count);
        $this->assertEquals($weight, $this->block->getTagWeight($tag));
    }

    /**
     * Testing of getSearchByTagUrl method
     */
    public function testGetSearchByTagUrl()
    {
        $tag = $this->getMockForAbstractClass('Aheadworks\Blog\Api\Data\TagInterface');
        $this->url->expects($this->atLeastOnce())
            ->method('getSearchByTagUrl')
            ->with($this->equalTo($tag))->willReturn(self::SEARCH_BY_TAG_URL);
        $this->assertEquals(self::SEARCH_BY_TAG_URL, $this->block->getSearchByTagUrl($tag));
    }

    /**
     * @return array
     */
    public function isCloudDataProvider()
    {
        return [[1, true], [0, false]];
    }

    /**
     * @return array
     */
    public function getTagWeightDataProvider()
    {
        return [
            [5, [5, 10, 15], 52.0],
            [10, [5, 10, 15], 100.0],
            [15, [5, 10, 15], 148.0]
        ];
    }
}
