<?php
use Magento\TestFramework\Helper\Bootstrap;

$tagsData = [
    ['name' => 'tag 1'],
    ['name' => 'second tag'],
    ['name' => 'third one']
];
foreach ($tagsData as $tagData) {
    /** @var \Aheadworks\Blog\Model\Tag $tag */
    $tag = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Tag');
    $tag->setData($tagData);
    $tag->isObjectNew(true);
    $tag->save();
}
