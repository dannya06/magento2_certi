<?php
namespace Icube\Magezon\Override\Block;

class Builder extends \Magezon\Builder\Block\Builder
{
	/**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'Icube_Magezon::builder.phtml';

	/**
	 * @var \Magezon\PageBuilder\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context        
	 * @param \Magezon\Builder\Model\CompositeConfigProvider   $configProvider 
	 * @param \Magezon\PageBuilder\Helper\Data                 $dataHelper     
	 * @param array                                            $data           
	 */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magezon\PageBuilder\Model\CompositeConfigProvider $configProvider,
        \Magezon\PageBuilder\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $configProvider, $data);
		$this->dataHelper = $dataHelper;
    }
} 