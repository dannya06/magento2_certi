<?php
use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;
use Magento\TestFramework\Helper\Bootstrap;

/** @var \Aheadworks\Blog\Model\Category $category */
$category = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Category');
$category
    ->setName('Cloud category')
    ->setUrlKey('cloudcategory')
    ->setStatus(\Aheadworks\Blog\Model\Category::STATUS_ENABLED)
    ->setSortOrder(0)
    ->setMetaTitle('Cloud category meta title')
    ->setMetaDescription('Cloud category meta description')
    ->setStoreIds(
        [
            Bootstrap::getObjectManager()
                ->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getId()
        ]
    );
$category->isObjectNew(true);
$category->save();

$postsData = [
    [
        'url_key' => 'cloudpost1',
        'tags' => ['cloud tag 1', 'cloud tag 2', 'cloud tag 3']
    ],
    [
        'url_key' => 'cloudpost2',
        'tags' => ['cloud tag 1', 'cloud tag 2']
    ],
    [
        'url_key' => 'cloudpost3',
        'tags' => ['cloud tag 1']
    ]
];
foreach ($postsData as $postData) {
    /** @var \Aheadworks\Blog\Model\Post $post */
    $post = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Post');
    $post->setData(
        array_merge(
            [
                'title' => 'Cloud post title',
                'short_content' => 'Cloud post short content',
                'content' => 'Cloud post content',
                'status' => PostStatus::PUBLICATION,
                'publish_date' => date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time()),
                'is_allow_comments' => 1,
                'meta_title' => 'Cloud post meta title',
                'meta_description' => 'Cloud post meta description',
                'store_ids' => [
                    Bootstrap::getObjectManager()
                        ->get('Magento\Store\Model\StoreManagerInterface')
                        ->getStore()
                        ->getId()
                ],
                'category_ids' => [
                    Bootstrap::getObjectManager()
                        ->create('Aheadworks\Blog\Model\Category')
                        ->load('cloudcategory', 'url_key')
                        ->getId()
                ]
            ],
            $postData
        )
    );
    $post->isObjectNew(true);
    $post->save();
}
