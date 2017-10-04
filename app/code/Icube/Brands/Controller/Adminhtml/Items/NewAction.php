<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Controller\Adminhtml\Items;

use Icube\Brands\Model\Items;

class NewAction extends \Icube\Brands\Controller\Adminhtml\Items
{
	/**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $_attrOptionCollectionFactory;
    
    protected  $registry;
    
    // public function __construct(
    // 	\Magento\Backend\App\Action\Context $context,
    // 	\Magento\Framework\Registry $registry,
    // 	\Magento\Backend\Model\View\Result\ForwardFactory $ForwardFactory , 
    // 	\Magento\Framework\View\Result\PageFactory $PF,
    // 	\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
    // 	array $data = [])
    // {
    // 	parent::__construct($context, $data);
    // 	$this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;
    // }

    public function execute()
    {
    	// $this->_forward('edit');
    	$model = $this->_objectManager->create(
    		'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
		)->setEntityTypeId(
			\Magento\Catalog\Model\Product::ENTITY
		);

		$scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
		$value = $scopeConfig->getValue(
			'icube_brands/config/attribute_name',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
		if ($value==null) {
			$this->messageManager->addError(__('Cannot re-sync brands. Brand attribute is still empty under Store > Configuration > Catalog > Brand'));
		}else{
			$model->loadByCode(\Magento\Catalog\Model\Product::ENTITY,$value);
			// echo "<pre>";
			// var_dump(get_class_methods($model));
			
			foreach($model->getOptions() as $option){
				// var_dump($option->debug());

				$item = $this->_objectManager->create('Icube\Brands\Model\Items');
				if($option->getValue()){
					$id = (int)$option->getValue();
					if ($id) {
						$item->load($id);
						if ($id != $item->getId()) {
							// throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
						}
					}

					$data = array(
						'name' => $option->getLabel(),
						'attribute_id' => $option->getValue(),
						'is_active' => 1,
					);
					$item->setData($data);
					// var_dump($item->debug());
					try{
						$item->save();
					}
					catch(\Exception $e){

					}
					// var_dump($item->debug());
					// die;

				}
			}
			$this->messageManager->addSuccess(__('All Brands Re-Synced'));
		}
		$this->_redirect('icube_brands/*/');
	
    }
}
