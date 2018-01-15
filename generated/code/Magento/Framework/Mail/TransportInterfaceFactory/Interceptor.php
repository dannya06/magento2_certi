<?php
namespace Magento\Framework\Mail\TransportInterfaceFactory;

/**
 * Interceptor class for @see \Magento\Framework\Mail\TransportInterfaceFactory
 */
class Interceptor extends \Magento\Framework\Mail\TransportInterfaceFactory implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = 'Magento\\Framework\\Mail\\TransportInterface')
    {
        $this->___init();
        parent::__construct($objectManager, $instanceName);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'create');
        if (!$pluginInfo) {
            return parent::create($data);
        } else {
            return $this->___callPlugins('create', func_get_args(), $pluginInfo);
        }
    }
}
