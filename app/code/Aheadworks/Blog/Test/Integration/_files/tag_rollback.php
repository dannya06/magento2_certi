<?php
use Magento\TestFramework\Helper\Bootstrap;

/** @var \Magento\Framework\Registry $registry */
$registry = Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Aheadworks\Blog\Model\Tag $tag */
$tag = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Tag');
$tag->load('fixturetag', 'name');
if ($tag->getId()) {
    $tag->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
