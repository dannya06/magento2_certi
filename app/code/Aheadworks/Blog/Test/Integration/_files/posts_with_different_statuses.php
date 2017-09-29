<?php
use Magento\TestFramework\Helper\Bootstrap;
use Aheadworks\Blog\Model\Source\Post\Status as PostStatus;

/** @var \Aheadworks\Blog\Model\Category $category */
$category = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Category');
$category
    ->setName('Post category')
    ->setUrlKey('postcategory')
    ->setStatus(\Aheadworks\Blog\Model\Category::STATUS_ENABLED)
    ->setSortOrder(0)
    ->setMetaTitle('Post category meta title')
    ->setMetaDescription('Post category meta description')
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
        'title' => 'Post',
        'url_key' => 'draftedpost',
        'status' => PostStatus::DRAFT
    ],
    [
        'title' => 'Post published',
        'url_key' => 'publishedpost',
        'status' => PostStatus::PUBLICATION,
        'publish_date' => date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time())
    ],
    [
        'title' => 'Post scheduled',
        'url_key' => 'scheduledpost',
        'status' => PostStatus::PUBLICATION,
        'publish_date' => date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, time() + (24 * 60 * 60))
    ]
];
foreach ($postsData as $postData) {
    /** @var \Aheadworks\Blog\Model\Post $post */
    $post = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Post');
    $post->setData(
        array_merge(
            [
                'short_content' => 'Post short content',
                'content' => 'Content',
                'is_allow_comments' => 1,
                'meta_title' => 'Post meta title',
                'meta_description' => 'Post meta description',
                'store_ids' => [
                    Bootstrap::getObjectManager()
                    ->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getId()
                ],
                'category_ids' => [
                    Bootstrap::getObjectManager()
                    ->create('Aheadworks\Blog\Model\Category')
                    ->load('postcategory', 'url_key')
                    ->getId()
                ],
                'tags' => ['posttag']
            ],
            $postData
        )
    );
    $post->isObjectNew(true);
    $post->save();
}
