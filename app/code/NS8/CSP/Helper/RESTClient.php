<?php
namespace NS8\CSP\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class RESTClient extends AbstractHelper
{
    private $configHelper;
    private $httpClientFactory;

    public function __construct(
        \NS8\CSP\Helper\Config $configHelper,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    ) {
        $this->configHelper = $configHelper;
        $this->httpClientFactory = $httpClientFactory;
    }

    // Request methods:
    public function get($url, $parameters = [], $headers = [], $timeout = 5)
    {
        return $this->execute($url, 'GET', $parameters, $headers, $timeout);
    }

    public function post($url, $parameters = [], $headers = [], $timeout = 5)
    {
        return $this->execute($url, "POST", $parameters, $headers, $timeout);
    }

    public function put($url, $parameters = [], $headers = [], $timeout = 5)
    {
        return $this->execute($url, 'PUT', $parameters, $headers, $timeout);
    }

    public function patch($url, $parameters = [], $headers = [], $timeout = 5)
    {
        return $this->execute($url, 'PATCH', $parameters, $headers, $timeout);
    }

    public function delete($url, $parameters = [], $headers = [], $timeout = 5)
    {
        return $this->execute($url, 'DELETE', $parameters, $headers, $timeout);
    }

    public function head($url, $parameters = [], $headers = [], $timeout = 5)
    {
        return $this->execute($url, 'HEAD', $parameters, $headers, $timeout);
    }

    public function execute($url, $method = "GET", $parameters = [], $headers = [], $timeout = 5)
    {
        $headers['magento-version'] = $this->configHelper->getMagentoVersion();
        $headers['extension-version'] = $this->configHelper->getExtensionVersion();

        $baseUrl = $this->configHelper->getApiBaseUrl().'/'.$url;
        $httpClient = $this->httpClientFactory->create();
        $httpClient->setUri($baseUrl);
        $httpClient->setConfig(
            ['timeout' => $timeout]
        );
        $httpClient->setMethod($method);
        $httpClient->setParameterPost($parameters);

        if (!empty($headers)) {
            $httpClient->setHeaders($headers);
        }

        $httpClient->setUrlEncodeBody(false);
        $response = $httpClient->request();

        $body = $response->getBody();

        if (isset($body) && $body !== '') {
            $json = json_decode($body);

            //  if standard code/data format, set the data
            if (isset($json->data)) {
                $response->data = $json->data;
            } else {
                $response->data = $json;
            }
        }

        return $response;
    }
}
