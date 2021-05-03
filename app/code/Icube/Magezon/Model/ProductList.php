<?php
namespace Icube\Magezon\Model;

class ProductList extends \Magezon\Core\Model\ProductList
{
    public function getProductCollection($source = 'latest', $numberItems = 8, $order = 'newestfirst', $conditions = '', $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID)
    {
        $store      = $this->_storeManager->getStore()->getId();
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('visibility', $this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)->addStoreFilter($store);
        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
            foreach ($conditions as $key => $condition) {
                    if (!empty($condition['attribute'])
                        && in_array($condition['attribute'], ['special_from_date', 'special_to_date'])
                    ) {
                        $conditions[$key]['value'] = date('Y-m-d H:i:s', strtotime($condition['value']));
                }
            }
            $this->rule->loadPost(['conditions' => $conditions]);
            $conditions = $this->rule->getConditions();
            $conditions->collectValidatedAttributes($collection);
            $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        }
        $collection->setPageSize($numberItems);

        switch ($source) {
            case 'latest':
            $collection->getSelect()->order('created_at DESC');
            break;

            case 'new':
            $this->_getNewProductCollection($collection);
            break;

            case 'bestseller':
            $this->_getBestSellerProductCollection($collection, $this->_storeManager->getStore()->getId());
            break;

            case 'onsale':
            $this->_getOnsaleProductCollection($collection, $this->_storeManager->getStore()->getId());
            break;

            case 'mostviewed':
            $this->_getMostViewedProductCollection($collection, $this->_storeManager->getStore()->getId());
            break;

            case 'wishlisttop':
            $this->_getWishlisttopProductCollection($collection, $this->_storeManager->getStore()->getId());
            break;

            case 'free':
            $collection->getSelect()->where('price_index.price = ?', 0);
            $collection->addAttributeToFilter('type_id', [
                'in' => [
                    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                    \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                    \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE
                ]
            ]);
            break;

            case 'featured':
            $collection->addAttributeToFilter('featured', ['eq' => 1]);
            break;

            case 'toprated':
            $this->_getTopRatedProductCollection($collection, $this->_storeManager->getStore()->getId());
            break;

            case 'random':
            $collection->getSelect()->order('RAND()');
            break;
        }

        if ($order!='default') {
            switch ($order) {
                case 'alphabetically':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->setOrder('name', 'ASC');
                    // usort($items, function($a, $b) {
                    //     return $a['name'] > $b['name'];
                    // });
                    break;

                case 'price_low_to_high':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->setOrder('price', 'ASC');
                    // usort($items, function($a, $b) {
                    //     return $a['price'] > $b['price'];
                    // });
                    break;

                case 'price_high_to_low':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->setOrder('price', 'DESC');
                    // usort($items, function($a, $b) {
                    //     return $a['price'] < $b['price'];
                    // });
                    break;

                case 'random':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->getSelect()->order('RAND()');
                    break;

                case 'newestfirst':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->setOrder('entity_id', 'DESC');
                    // usort($items, function($a, $b) {
                    //     $aval = strtotime($a['created_at']);
                    //     $bval = strtotime((int) $b['created_at']);
                    //     if ($aval == $bval) {
                    //         return 0;
                    //     }
                    //     return $aval < $bval ? 1 : -1;
                    // });
                    break;

                case 'oldestfirst':
                    $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                    $collection->setOrder('entity_id', 'ASC');
                    // usort($items, function($a, $b) {
                    //     $aval = strtotime($a['created_at']);
                    //     $bval = strtotime((int) $b['created_at']);
                    //     if ($aval == $bval) {
                    //         return 0;
                    //     }
                    //     return $aval > $bval ? 1 : -1;
                    // });
                    break;

                case 'product_attr':
                    $collection->setOrder('product_position', 'ASC');
                    // usort($items, function($a, $b) {
                    //     return (isset($a['product_position']) ? (int) $a['product_position'] : 0) > (isset($b['product_position']) ? (int) $b['product_position'] : 0);
                    // });
                    break;
            }
        }

        $items = $collection->getItems();

        return $items;
    }
}