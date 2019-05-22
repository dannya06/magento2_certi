<?php

namespace Icube\Paypal\Model\Api;

class Nvp extends \Magento\Paypal\Model\Api\Nvp
{

    /**
     * Do the API call
     *
     * @param string $methodName
     * @param array $request
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function call($methodName, array $request)
    {

        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customnvp.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);

        // $logger->info(print_r($request,1));

        $request = $this->_addMethodToRequest($methodName, $request);
        $eachCallRequest = $this->_prepareEachCallRequest($methodName);
        if ($this->getUseCertAuthentication()) {
            $key = array_search('SIGNATURE', $eachCallRequest);
            if ($key) {
                unset($eachCallRequest[$key]);
            }
        }
        $request = $this->_exportToRequest($eachCallRequest, $request);
        $debugData = ['url' => $this->getApiEndpoint(), $methodName => $request];
        // $logger->info("METHOD : ".$request["METHOD"].", CURRENCYCODE : ".$request['CURRENCYCODE']);
        if ($request["METHOD"] == "SetExpressCheckout" || $request["METHOD"] == "DoExpressCheckoutPayment")
        {
            
            if($request['CURRENCYCODE'] == "IDR") {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $currencyHelper = $objectManager->get('Magento\Directory\Helper\Data');
                
                $payAmt = $request['AMT'];
                $payShippingAmt = $request['SHIPPINGAMT'];
                $payItemAmt = $request['ITEMAMT'];
                $payTaxAmt = $request['TAXAMT'];
                $conAmt = round($currencyHelper->currencyConvert($payAmt, "IDR", "USD"), 2);
                $conShippingAmt = round($currencyHelper->currencyConvert($payShippingAmt, "IDR", "USD"), 2);
                $conItemAmt = round($currencyHelper->currencyConvert($payItemAmt, "IDR", "USD"), 2);
                $conTaxAmt = round($currencyHelper->currencyConvert($payTaxAmt, "IDR", "USD"), 2);
                //set converted USD amount to request api
        
                $request['CURRENCYCODE'] = "USD";
                $request['AMT'] = $conAmt;
                $request['SHIPPINGAMT'] = $conShippingAmt;
                $request['ITEMAMT'] = $conItemAmt;
                $request['TAXAMT'] = $conTaxAmt;
            }
        }
        // $logger->info(print_r($request,1));
        try {
            $http = $this->_curlFactory->create();
            $config = ['timeout' => 60, 'verifypeer' => $this->_config->getValue('verifyPeer')];
            if ($this->getUseProxy()) {
                $config['proxy'] = $this->getProxyHost() . ':' . $this->getProxyPort();
            }
            if ($this->getUseCertAuthentication()) {
                $config['ssl_cert'] = $this->getApiCertificate();
            }
            $http->setConfig($config);
            // $logger->info($this->getApiEndpoint());
            $http->write(
                \Zend_Http_Client::POST,
                $this->getApiEndpoint(),
                '1.1',
                $this->_headers,
                $this->_buildQuery($request)
            );
            $response = $http->read();
            // $logger->info("A");
        } catch (\Exception $e) {
            $debugData['http_error'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $this->_debug($debugData);
            throw $e;
            // $logger->info("B");
        }

        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);
        $response = $this->_deformatNVP($response);

        // $logger->info(print_r($response,1));

        $debugData['response'] = $response;
        $this->_debug($debugData);

        $response = $this->_postProcessResponse($response);

        // handle transport error
        if ($http->getErrno()) {
            $this->_logger->critical(
                new \Exception(
                    sprintf('PayPal NVP CURL connection error #%s: %s', $http->getErrno(), $http->getError())
                )
            );
            $http->close();

            throw new \Magento\Framework\Exception\LocalizedException(
                __('Payment Gateway is unreachable at the moment. Please use another payment option.')
            );
        }

        // cUrl resource must be closed after checking it for errors
        $http->close();

        if (!$this->_validateResponse($methodName, $response)) {
            $this->_logger->critical(new \Exception(__('PayPal response hasn\'t required fields.')));
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while processing your order.')
            );
        }

        $this->_callErrors = [];
        if ($this->_isCallSuccessful($response)) {
            if ($this->_rawResponseNeeded) {
                $this->setRawSuccessResponseData($response);
            }
            return $response;
        }
        $this->_handleCallErrors($response);
        return $response;
    }
}