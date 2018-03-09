<?php
namespace Icube\Prism\Controller\SearchProducts;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;

Class SearchProducts extends \Magento\Framework\App\Action\Action {


    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        \Magento\Catalog\Model\ProductRepository $productRepo,
        \Magento\CatalogInventory\Model\Stock\Item $stock
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->pageResultFactory = $context->getResultFactory();
        $this->productRepo = $productRepo;
        $this->stock = $stock;
    }

     public function execute(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $_POST = json_decode(file_get_contents('php://input'),true);
            $products = array();
            $count = 0;
            $skus = array();

            $fieldvalue = '%'.$_POST['must'][0]['query_string']['query'].'%';
            
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productcollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                            ->addAttributeToSelect('*')
                            ->addAttributeToFilter(
                                array(
                                    array('attribute' => 'name', 'like' => '%'.$fieldvalue.'%'),
                                    array('attribute' => 'sku', 'like' => '%'.$fieldvalue.'%')
                                )
                            )
                            // ->addAttributeToFilter(
                            //     array(
                            //         array('attribute' => 'visibility', 'eq' => 3),
                            //         array('attribute' => 'visibility', 'eq' => 4)
                            //     )
                            // )
                            ->setPage(1,$_POST['size']);
            $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
            foreach ($productcollection as $value){

                if($value->getTypeId() == 'configurable') {
                    $children = $value->getTypeInstance()->getUsedProducts($value);
                    foreach ($children as $child){
                        $stock = $this->stock->load($child->getEntityId(),'product_id');
                        if($stock->getQty() > 0 && $stock->getIsInStock()) {
                            $count++;   
                            $productArr = array();
                            // $productArr['id']= $value->getEntityId();
                            $productArr['id']= $child->getSku();
                            $skus[] = $child->getSku();
                            $productArr['name'] = $value->getName()." - ".$child->getSku();
                            $escapers =     array("\\",     "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c");
                            $replacements = array("\\\\", "\\/", "\\\"", "", "\\r", "\\t",  "\\f",  "\\b");
                            $descEscape = str_replace($escapers, $replacements, $child->getDescription());
                            $productArr['description'] = $descEscape;
                            $image = $child->getData('thumbnail');
                            if(isset($image)) {
                                $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product'.$image;
                                $productArr['image_urls'] = array($imageUrl);
                            } else {
                                $productArr['image_urls'] = array();
                            }
                            $productArr['stock'] = $stock->getQty();
                            $productArr['price'] = $child->getPrice();
                            $discountAmount = $child->getPrice() - $child->getFinalPrice(); ; 
                            $productArr['discount']= array(
                                    'discount_type'=>'NOMINAL',
                                    'amount'=> (string)abs($discountAmount),
                                    );
                            $productArr['currency_code'] = 'IDR';
                            $products[] = $productArr;
                        }
                    }
                } else if($value->getTypeId() == 'simple') {
                    $stock = $this->stock->load($value->getEntityId(),'product_id');
                    if($stock->getQty() > 0 && $stock->getIsInStock() && !in_array($value->getSku(), $skus)) {
                        $count++;
                        $productArr = array();
                        // $productArr['id']= $value->getEntityId();
                        $productArr['id']= $value->getSku();
                        $skus[] = $value->getSku();
                        $prodName = $value->getName();
                        $parentIds = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($value->getId());
                        if(isset($parentIds[0])){
                            $parentProd = $objectManager->create('Magento\Catalog\Model\Product')->load($parentIds[0]);
                            $prodName = $parentProd->getName();
                        }
                        $productArr['name'] = $prodName." - ".$value->getSku();
                        $escapers =     array("\\",     "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c");
                        $replacements = array("\\\\", "\\/", "\\\"", "", "\\r", "\\t",  "\\f",  "\\b");
                        $descEscape = str_replace($escapers, $replacements, $value->getDescription());
                        $productArr['description'] = $descEscape;
                        $image = $value->getData('thumbnail');
                        if(isset($image)) {
                            $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product'.$image;
                            $productArr['image_urls'] = array($imageUrl);
                        } else {
                            $productArr['image_urls'] = array();
                        }
                        $productArr['stock'] = $stock->getQty();
                        $productArr['price'] = $value->getPrice();
                        $discountAmount = $value->getPrice() - $value->getFinalPrice(); ; 
                        $productArr['discount']= array(
                                'discount_type'=>'NOMINAL',
                                'amount'=> (string)abs($discountAmount),
                                );
                        $productArr['currency_code'] = 'IDR';
                        $products[] = $productArr;
                    }
                }
            }
            
            $result = array(
                        'status'=>'success',
                        'data'=> array(
                            'total'=>$count,
                            'results'=>$products,
                            ),
                        );

            // echo json_encode($result);
            echo json_encode($result, JSON_UNESCAPED_SLASHES);

            
        }
    }  
}   
