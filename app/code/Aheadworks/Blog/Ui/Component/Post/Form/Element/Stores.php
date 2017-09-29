<?php
namespace Aheadworks\Blog\Ui\Component\Post\Form\Element;

use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Stores
 * @package Aheadworks\Blog\Ui\Component\Post\Form\Element
 */
class Stores extends MultiSelect
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Stores constructor.
     *
     * @param ContextInterface $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\System\Store $storeOptions
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\System\Store $storeOptions,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $storeOptions, $components, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if ($this->storeManager->hasSingleStore()) {
            $config['visible'] = false;
            $this->setData('config', $config);
        }
        parent::prepare();
    }
}
