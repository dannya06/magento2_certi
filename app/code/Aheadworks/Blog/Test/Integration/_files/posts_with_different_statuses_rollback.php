<?php
use Magento\TestFramework\Helper\Bootstrap;

/** @var \Magento\Framework\Registry $registry */
$registry = Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Aheadworks\Blog\Model\Category $category */
$category = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Category');
$category->load('postcategory', 'url_key');
if ($category->getId()) {
    $category->delete();
}

$postUrlKeys = ['draftedpost', 'publishedpost', 'scheduledpost'];
foreach ($postUrlKeys as $urlKey) {
    /** @var \Aheadworks\Blog\Model\Post $post */
    $post = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Post');
    $post->load($urlKey, 'url_key');
    if ($post->getId()) {
        $post->delete();
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
