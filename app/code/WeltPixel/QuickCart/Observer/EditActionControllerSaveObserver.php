<?php

namespace WeltPixel\QuickCart\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * FrontendOptionsEditActionControllerSaveObserver observer
 */
class EditActionControllerSaveObserver implements ObserverInterface {
	
	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;
	
	/**
	 * @var \Magento\Framework\Module\Dir\Reader
	 */
	protected $_dirReader;
	
	/**
	 * @var \Magento\Framework\Filesystem\Directory\WriteFactory
	 */
	protected $_writeFactory;
	
	/**
	 * var \WeltPixel\QuickCart\Helper\Data
	 */
	protected $_helper;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;
	
	/**
	 * Store collection
	 * storeId => \Magento\Store\Model\Store
	 *
	 * @var array
	 */
	protected $_storeCollection;
	
	
	/**
	 * Constructor
	 *
	 * @param \WeltPixel\QuickCart\Helper\Data $helper
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Framework\Module\Dir\Reader $dirReader
	 * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct(
			\WeltPixel\QuickCart\Helper\Data $helper,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Framework\Module\Dir\Reader $dirReader,
			\Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
			\Magento\Store\Model\StoreManagerInterface $storeManager
	) {
		$this->_helper = $helper;
		$this->_scopeConfig = $scopeConfig;
		$this->_dirReader = $dirReader;
		$this->_writeFactory = $writeFactory;
		$this->_storeManager = $storeManager;
		$this->_storeCollection = $this->_storeManager->getStores();
	}
	
	/**
	 * Save quickcart options in file
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return void
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {
		/* Store view specific less generation */
		$this->_generateStoreViewSpecificLess();
	}
	
	/**
	 * @return void
	 */
	protected function _generateStoreViewSpecificLess() {
		$content = '/* Generated Less from WeltPixel_QuickCart */' . PHP_EOL;
		
		$lessTemplate = $this->_dirReader->getModuleDir('', 'WeltPixel_QuickCart') . DIRECTORY_SEPARATOR .
		                'data' . DIRECTORY_SEPARATOR . 'storeview_template.less';
		
		
		$lessVariables = $this->_getLessVariables();
		
		
		foreach ($this->_storeCollection as $store) {
			$lessValues = $this->_getLessValues($store);
			$content .= str_replace($lessVariables, $lessValues, file_get_contents($lessTemplate));
		}
		
		$directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_QuickCart');
		
		$lessPath =
				DIRECTORY_SEPARATOR . 'frontend'.
				DIRECTORY_SEPARATOR . 'web' .
				DIRECTORY_SEPARATOR . 'css' .
				DIRECTORY_SEPARATOR . 'source' .
				DIRECTORY_SEPARATOR . '_module.less';
		
		$writer = $this->_writeFactory->create($directoryCode, \Magento\Framework\Filesystem\DriverPool::FILE);
		$file = $writer->openFile($lessPath, 'w');
		try {
			$file->lock();
			try {
				$file->write($content);
			} finally {
				$file->unlock();
			}
		} finally {
			$file->close();
		}
	}
	
	/**
	 * @return array
	 */
	private function _getLessVariables() {
		return array(
				'@storeViewClass',
				'@headerHeight',
				'@headerBackground',
				'@headerTextColor',
				'@subtotalBackground',
				'@subtotalTextColor',
		);
	}
	
	/**
	 * @param \Magento\Store\Model\Store
	 * @return array
	 */
	private function _getLessValues(\Magento\Store\Model\Store $store) {
		$storeId = $store->getStoreId();
		$storeCode = $store->getData('code');
		$storeClassName = '.store-view-' . preg_replace('#[^a-z0-9]+#', '-', strtolower($storeCode));
		
		return array(
				$storeClassName,
				$this->_helper->getHeaderHeight($storeId),
				$this->_helper->getHeaderBackground($storeId),
				$this->_helper->getHeaderTextColor($storeId),
				$this->_helper->getSubtotalBackground($storeId),
				$this->_helper->getSubtotalTextColor($storeId),
		);
	}
}
