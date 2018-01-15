<?php
namespace Magento\Framework\Url;

/**
 * Interceptor class for @see \Magento\Framework\Url
 */
class Interceptor extends \Magento\Framework\Url implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Route\ConfigInterface $routeConfig, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\Url\SecurityInfoInterface $urlSecurityInfo, \Magento\Framework\Url\ScopeResolverInterface $scopeResolver, \Magento\Framework\Session\Generic $session, \Magento\Framework\Session\SidResolverInterface $sidResolver, \Magento\Framework\Url\RouteParamsResolverFactory $routeParamsResolverFactory, \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Url\RouteParamsPreprocessorInterface $routeParamsPreprocessor, $scopeType, array $data = array(), \Magento\Framework\Url\HostChecker $hostChecker = null, \Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->___init();
        parent::__construct($routeConfig, $request, $urlSecurityInfo, $scopeResolver, $session, $sidResolver, $routeParamsResolverFactory, $queryParamsResolver, $scopeConfig, $routeParamsPreprocessor, $scopeType, $data, $hostChecker, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUrl');
        if (!$pluginInfo) {
            return parent::getUrl($routePath, $routeParams);
        } else {
            return $this->___callPlugins('getUrl', func_get_args(), $pluginInfo);
        }
    }
}
