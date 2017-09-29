<?php
use Magento\TestFramework\Helper\Bootstrap;

/** @var \Aheadworks\Blog\Model\Tag $tag */
$tag = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Tag');
$tag->setName('fixturetag');
$tag->isObjectNew(true);
$tag->save();
