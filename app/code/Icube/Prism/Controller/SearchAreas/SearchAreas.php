<?php
namespace Icube\Prism\Controller\SearchAreas;


Class SearchAreas extends \Magento\Framework\App\Action\Action {

	public function execute(){

		if($_SERVER['REQUEST_METHOD'] === 'GET'){
	    	$name = $_GET['name'];
	    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$moduleManager = $objectManager->get('Magento\Framework\Module\Manager');

			$resultData = array();
			if($moduleManager->isEnabled('Icube_City')) {
				$connection = $resource->getConnection();
				$tableName = $resource->getTableName('city'); 

				$query = "Select * FROM " . $tableName ." WHERE kecamatan LIKE '%".$name."%'";;
				$results = $connection->fetchAll($query);

				foreach ($results as $value) {
					$resultArr = array();
					$region = $objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection')
					->addFieldToFilter('code', $value['region_code'])
					->load()->getFirstItem();

					$resultArr['label']=$value['kecamatan'].','.$value['city'];
			        $resultArr['provider']='custom';
			        $resultArr['custom'] = array(
			                   'country'=>'IDN',
			                   'first_level'=>$region->getDefaultName(),
			                   'second_level'=>$value['city'],
			                   'third_level'=>$value['kecamatan'],
			                   'region_id'=>$region->getRegionId()
			                   );

					$resultData[]=$resultArr;
				}
			} else {
				$results = $objectManager->get('Magento\Directory\Model\ResourceModel\Region\Collection')
				->addFieldToFilter('default_name', ['like' => '%'.$name.'%'])
				->load();
				foreach ($results as $value) {
					$resultArr = array();
					$resultArr['label']=$value['default_name'];
			        $resultArr['provider']='custom';
			        $resultArr['custom'] = array(
			                   'country'=>'IDN',
			                   'first_level'=>$value['default_name'],
			                   'second_level'=>"",
			                   'third_level'=>"",
			                   'region_id'=>$value['region_id']
			                   );
					$resultData[]=$resultArr;
				}
			}
			
			$result = array(
	                    'status'=>'success',
	                    'data'=> array(
	                        'results'=>$resultData,
	                        ),
	                );

			echo json_encode($result);
		}
	}
}
