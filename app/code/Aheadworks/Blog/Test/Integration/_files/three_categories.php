<?php
use Aheadworks\Blog\Model\Category;
use Magento\TestFramework\Helper\Bootstrap;

$categoriesData = [
    [
        'name' => 'Category 1',
        'url_key' => 'fixturecategory1',
        'status' => Category::STATUS_ENABLED
    ],
    [
        'name' => 'Second category',
        'url_key' => 'fixturecategory2',
        'status' => Category::STATUS_ENABLED
    ],
    [
        'name' => 'Cat 3',
        'url_key' => 'fixturecategory3',
        'status' => Category::STATUS_DISABLED
    ]
];
foreach ($categoriesData as $categoryData) {
    /** @var \Aheadworks\Blog\Model\Category $category */
    $category = Bootstrap::getObjectManager()->create('Aheadworks\Blog\Model\Category');
    $category->setData(
        array_merge(
            [
                'sort_order' => 0,
                'meta_title' => 'Category meta title',
                'meta_description' => 'Category meta description',
                'store_ids' => [
                    Bootstrap::getObjectManager()
                        ->get('Magento\Store\Model\StoreManagerInterface')
                        ->getStore()
                        ->getId()
                ]
            ],
            $categoryData
        )
    );
    $category->isObjectNew(true);
    $category->save();
}
