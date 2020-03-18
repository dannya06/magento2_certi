<?php
/**
 * Ipay88 Inc
 * @author Ipay88 Inc <pvchi@ipay88.com>
 * @package Ipay88\Lib
 */

class Ipay88_Signature
{
    protected $source;

    protected $signature;

    protected $merchantCode;

    protected $mechantKey;

    protected $refNo;

    protected $amount;

    protected $currency;

    protected $paymentId;

    protected $status;

    /**
     * @return mixed
     */
    public function getMerchantCode()
    {
        return $this->merchantCode;
    }

    /**
     * @param mixed $merchantCode
     */
    public function setMerchantCode($merchantCode)
    {
        $this->merchantCode = $merchantCode;
    }

    /**
     * @return mixed
     */
    public function getMechantKey()
    {
        return $this->mechantKey;
    }

    /**
     * @param mixed $mechantKey
     */
    public function setMechantKey($mechantKey)
    {
        $this->mechantKey = $mechantKey;
    }

    /**
     * @return mixed
     */
    public function getRefNo()
    {
        return $this->refNo;
    }

    /**
     * @param mixed $refNo
     */
    public function setRefNo($refNo)
    {
        $this->refNo = $refNo;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param mixed $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param mixed $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }



    public function __construct($source = null)
    {
        if($source) {
            $this->source = $source;
            $this->setSignature($this->generateSignature());
        }
    }

    /**
     * @param $options
     */
    public function init($options) {
        $this->setMerchantCode(isset($options['MerchantCode']) ? $options['MerchantCode'] : '');
        $this->setAmount(isset($options['Amount']) ? $options['Amount'] : 0);
        $this->setRefNo(isset($options['RefNo']) ? $options['RefNo'] : '');
        $this->setPaymentId(isset($options['PaymentId']) ? $options['PaymentId'] : '');
        $this->setCurrency(isset($options['Currency']) ? $options['Currency'] : '');
        $this->setStatus(isset($options['Status']) ? $options['Status'] : '');
    }

    /**
     * @param null $data
     * @return string
     */
    public function getRequestSignature($data = null) {
        $params = ['merchantKey', 'merchantCode', 'refNo', 'amount', 'currency'];
        foreach ($params as $key => $value) {
            if (isset($data[$key])) {
                $this->$key = $value;
            }
        }
        $source = $this->getMechantKey() . $this->getMerchantCode() . $this->getRefNo() . $this->getHashAmount() . $this->getCurrency();

        return $this->generateSignature($source);
    }

    /**
     * Response Signature
     * @param null $data
     * @return string
     */
    public function getResponseSignature($data = null) {
        $params = ['merchantKey', 'merchantCode', 'paymentId', 'refNo', 'amount', 'currency', 'status'];
        foreach ($params as $key => $value) {
            if (isset($data[$key])) {
                $this->$key = $value;
            }
        }
        $source = $this->getMechantKey() . $this->getMerchantCode() . $this->getPaymentId() . $this->getRefNo() . $this->getHashAmount() . $this->getCurrency() . $this->getStatus();

        return $this->generateSignature($source);
    }

    /**
     * @param null $source
     * @return string
     */
    public function generateSignature($source = null) {
        $source = $source ? : $this->getSource();

        return base64_encode($this->hex2bin(sha1($source)));
    }

    protected function hex2bin($hexSource)
    {
        $bin = '';
        for ($i = 0; $i < strlen($hexSource); $i = $i + 2)
        {
            $bin .= chr(hexdec(substr($hexSource, $i, 2)));
        }
        return $bin;
    }

    public function getHashAmount($orderAmount = null) {
        $orderAmount = $orderAmount ? : $this->getAmount();
        $hashAmount = str_replace(".", "", $orderAmount);
        $hashAmount = str_replace(",", "", $hashAmount);

        return $hashAmount;
    }
}