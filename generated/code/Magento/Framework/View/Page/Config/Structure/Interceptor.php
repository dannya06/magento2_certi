<?php
namespace Magento\Framework\View\Page\Config\Structure;

/**
 * Interceptor class for @see \Magento\Framework\View\Page\Config\Structure
 */
class Interceptor extends \Magento\Framework\View\Page\Config\Structure implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function addAssets($name, $attributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addAssets');
        if (!$pluginInfo) {
            return parent::addAssets($name, $attributes);
        } else {
            return $this->___callPlugins('addAssets', func_get_args(), $pluginInfo);
        }
    }
}
