<?php
namespace Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend\Templates;

/**
 * Interceptor class for @see \Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend\Templates
 */
class Interceptor extends \Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend\Templates implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validate');
        if (!$pluginInfo) {
            return parent::validate($object);
        } else {
            return $this->___callPlugins('validate', func_get_args(), $pluginInfo);
        }
    }
}
