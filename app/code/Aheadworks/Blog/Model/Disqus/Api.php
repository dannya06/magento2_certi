<?php
namespace Aheadworks\Blog\Model\Disqus;

use Aheadworks\Blog\Model\Config;

/**
 * Disqus Api
 * @package Aheadworks\Blog\Model\Disqus
 */
class Api
{
    /**
     * API version
     *
     * @var string
     */
    protected $version = '3.0';

    /**
     * Default request method
     *
     * @var string
     */
    protected $method = \Zend_Http_Client::GET;

    /**
     * Default output type
     *
     * @var string
     */
    protected $outputType = 'json';

    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    protected $curlFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Api constructor.
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param Config $config
     */
    public function __construct(
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        Config $config
    ) {
        $this->curlFactory = $curlFactory;
        $this->config = $config;
    }

    /**
     * Send request
     *
     * @param string $resource
     * @param array $args
     * @return array|bool
     */
    public function sendRequest($resource, $args = [])
    {
        /** @var \Magento\Framework\HTTP\Adapter\Curl $curl */
        $curl = $this->curlFactory->create();
        $curl->setConfig(['timeout' => 60, 'header' => false]);
        $curl->write($this->method, $this->getEndpoint($resource, $args));
        try {
            $response = \Zend_Json::decode($curl->read());
            $response = isset($response['response']) ? $response['response'] : false;
        } catch (\Exception $e) {
            $response = false;
        }
        $curl->close();
        return $response;
    }

    /**
     * Get prepared endpoint url
     *
     * @param string $resource
     * @param array $args
     * @return string
     */
    protected function getEndpoint($resource, $args = [])
    {
        $endpoint = 'https://disqus.com/api/' . $this->version . '/' .
            $resource . '.' . $this->outputType;
        $rawParams = array_merge(
            ['api_secret' => $this->config->getValue(Config::XML_GENERAL_DISQUS_SECRET_KEY)],
            $args
        ); // todo: store ID

        $params = [];
        foreach ($rawParams as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $params[] = $key . '[]=' . urlencode($item);
                }
            } else {
                $params[] = $key . '=' . urlencode($value);
            }
        }
        $endpoint .= '?' . implode('&', $params);

        return $endpoint;
    }
}
