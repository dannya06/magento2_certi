<?php
namespace Icube\Bundle\Block;

class Init extends \Magento\Framework\View\Element\Template
{
    protected $_assetRepo;
	protected $helper;
	
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Icube\Bundle\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_assetRepo = $assetRepo;
        $this->helper = $helper;
    }

    public function getAssetRepo($file)
    {
        return $this->_assetRepo->getUrl($file);
    }
    
    public function isEnableJsBundling()
    {
        return $this->helper->getConfigValue('icube_bundle/bundle_js/bundle_js_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
