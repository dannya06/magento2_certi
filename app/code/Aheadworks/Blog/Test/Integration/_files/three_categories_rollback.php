<?php
use Magento\TestFramework\Helper\Bootstrap;

/** @var \Magento\Framework\Registry $registry */
$registry = Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$categoryUrlKeys = ['fixturecategory1', 'fixturecategory2', 'fixturecategory3'];
foreach ($categoryUrlKeys as $urlKey) {
    /** @var \Aheadworks\Blog\Model\Category $category */
    $category = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Category');
    $category->load($urlKey, 'url_key');
    if ($category->getId()) {
        $category->delete();
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
