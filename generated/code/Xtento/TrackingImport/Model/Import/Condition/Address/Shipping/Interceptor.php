<?php
namespace Xtento\TrackingImport\Model\Import\Condition\Address\Shipping;

/**
 * Interceptor class for @see \Xtento\TrackingImport\Model\Import\Condition\Address\Shipping
 */
class Interceptor extends \Xtento\TrackingImport\Model\Import\Condition\Address\Shipping implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Rule\Model\Condition\Context $context, \Magento\Directory\Model\Config\Source\Country $directoryCountry, \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion, \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods, \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods, \Xtento\TrackingImport\Model\Import\Condition\CustomFactory $conditionCustomFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $directoryCountry, $directoryAllregion, $shippingAllmethods, $paymentAllmethods, $conditionCustomFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getOperatorSelectOptions()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOperatorSelectOptions');
        if (!$pluginInfo) {
            return parent::getOperatorSelectOptions();
        } else {
            return $this->___callPlugins('getOperatorSelectOptions', func_get_args(), $pluginInfo);
        }
    }
}
