<?php 
namespace Icube\Cashback\Model;

class StoreCredit{

	protected $state;

	protected $registry;

	protected $cashbackFactory;

	protected $orderInterface;

	protected $customerRepository;

	protected $storeManager;

	protected $customerStoreCreditService;

	protected $transactionFactory;

	public function __construct(	
		\Magento\Framework\App\State $state,
		\Magento\Framework\Registry $registry,
		\Icube\Cashback\Model\CashbackFactory $cashbackFactory,
		\Magento\Sales\Api\Data\OrderInterface $orderInterface,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Aheadworks\StoreCredit\Api\TransactionRepositoryInterface $transactionRepository,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Aheadworks\StoreCredit\Api\CustomerStoreCreditManagementInterface $customerStoreCreditService,
		\Aheadworks\StoreCredit\Model\TransactionFactory $transactionFactory
	){
		$this->state = $state;
		$this->registry = $registry;
		$this->cashbackFactory = $cashbackFactory;
		$this->orderInterface = $orderInterface;
		$this->customerRepository = $customerRepository;
		$this->transactionRepository = $transactionRepository;
		$this->storeManager = $storeManager;
		$this->customerStoreCreditService = $customerStoreCreditService;
		$this->transactionFactory = $transactionFactory;
	}

	public function createTransaction()
	{
		try {
			$this->state->setAreaCode('adminhtml');            
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			 
		}

		if ($this->registry->registry('isSecureArea')) {
			$this->registry->unregister('isSecureArea');
		}

		$this->registry->register('isSecureArea', true);

		$cashbackList = $this->cashbackFactory->create()->getCollection()->addFieldToFilter('status',array('eq'=>'NEW'));
		$this->proceedTransaction($cashbackList,false);
		
		$cashbackListFailed = $this->cashbackFactory->create()->getCollection()->addFieldToFilter('status',array('eq'=>'FAILED'));
		$this->proceedTransaction($cashbackListFailed,true);
		
	}
	

	protected function proceedTransaction($cashbackList,$is_failed){
		foreach($cashbackList as $cashback){
			$order = $this->orderInterface->load($cashback->getOrderId());
			if($order->getCustomerId()!== null){

				if($is_failed){
					// check if failed status already created store credit in aheadwork
					$transactionList = $this->transactionFactory->create()->getCollection()->addFieldToFilter('comment_to_customer',array('like'=> '% '.$order->getIncrementId().' %'));
					if(count($transactionList) <= 0){
						continue;
					}
				}
				$customer = $this->customerRepository->getById($order->getCustomerId());

				$comment = 'Cashback : "'.$cashback->getPromoName().'" from order #'.$order->getIncrementId();

				$transactionData = array(
					'customer_id' => $customer->getId(),
					'customer_name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
					'customer_email' =>$customer->getEmail(),
					'comment_to_customer' => $comment,
					'comment_to_admin' => '',
					'balance' => $cashback->getCashbackAmount(),
					'website_id' => $this->storeManager->getStore()->getWebsiteId()
				);

				$this->customerStoreCreditService->resetCustomer();
            	try{
            		$this->customerStoreCreditService->saveAdminTransaction($transactionData);
            	}catch(\Exception $exception){
            		$cashback->setDescription($exception->getMessage());
            		$cashback->setStatus('FAILED')->save();
            	}
            	

            	$cashback->setStatus('COMPLETED')->save();
			}else{

			}

		}
	}


}			