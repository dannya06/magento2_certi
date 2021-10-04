<?php

namespace Icube\Magezon\Plugin\Element;

class ProductList
{
	/**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function aroundPrepareConditionTab(\Magezon\Builder\Data\Element\AbstractElement $subject)
    {
    	$condition = $subject->addTab(
            'tab_condition',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Condition')
                ]
            ]
        );

	        $container1 = $condition->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 10
	            ]
		    );

		        $container1->addChildren(
		            'max_items',
		            'number',
		            [
						'sortOrder'       => 20,
						'key'             => 'max_items',
						'defaultValue'    => 10,
						'templateOptions' => [
							'label'   => __('Total Items')
		                ]
		            ]
		        );

	        $container2 = $condition->addContainerGroup(
	            'container2',
	            [
					'sortOrder' => 20
	            ]
		    );

		    	$container2->addChildren(
		            'orer_by',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'orer_by',
						'defaultValue'    => 'default',
						'templateOptions' => [
							'label'   => __('Order By'),
							'options' => $subject->getOrderByOptions()
		                ]
		            ]
		        );

		    	$container2->addChildren(
		            'show_out_of_stock',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'show_out_of_stock',
						'templateOptions' => [
							'label' => __('Display Out of Stock Products')
		                ]
		            ]
		        );

	    	$condition->addChildren(
	            'condition',
	            'condition',
	            [
					'sortOrder'       => 30,
					'key'             => 'condition',
					'templateOptions' => [
						'label' => __('Conditions')
	                ]
	            ]
	        );

    	return $condition;
    }
	
    public function aroundGetOrderByOptions()
    {
        return [
            [
                'label' => __('Default'),
                'value' => 'default'
            ],
            [
                'label' => __('Alphabetically'),
                'value' => 'alphabetically'
            ],
            [
                'label' => __('Price: Low to High'),
                'value' => 'price_low_to_high'
            ],
            [
                'label' => __('Price: High to Low'),
                'value' => 'price_high_to_low'
            ],
            [
                'label' => __('Random'),
                'value' => 'random'
            ],
            [
                'label' => __('Newest First'),
                'value' => 'newestfirst'
            ],
            [
                'label' => __('Oldest First'),
                'value' => 'oldestfirst'
			],
			[
                'label' => __('Latest'),
                'value' => 'latest'
            ],
            [
                'label' => __('New Arrival'),
                'value' => 'new'
            ],
            [
                'label' => __('Best Sellers'),
                'value' => 'bestseller'
            ],
            [
                'label' => __('On Sale'),
                'value' => 'onsale'
            ],
            [
                'label' => __('Most Viewed'),
                'value' => 'mostviewed'
            ],
            [
                'label' => __('Wishlist Top'),
                'value' => 'wishlisttop'
            ],
            [
                'label' => __('Top Rated'),
                'value' => 'toprated'
            ],
            [
                'label' => __('Featured'),
                'value' => 'featured'
            ],
            [
                'label' => __('Free'),
                'value' => 'free'
            ],
            [
                'label' => __('Random'),
                'value' => 'random'
            ]
        ];
    }
}