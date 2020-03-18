<?php
/**
 * Ipay88 Inc
 * @author Ipay88 Inc <pvchi@ipay88.com>
 * @package Ipay88\Lib
 */

class Ipay88_Indonesia
{
    const URL_PAYMENT_PRODUCTION = 'https://payment.ipay88.co.id/epayment/entry.asp';
    const URL_PAYMENT_SANDBOX    = 'https://sandbox.ipay88.co.id/epayment/entry.asp';

    const URL_PAYMENT_PRODUCTION_VERSION2 = 'https://payment.ipay88.co.id/epayment/entry_v2.asp';
    const URL_PAYMENT_SANDBOX_VERSION2 = 'https://sandbox.ipay88.co.id/epayment/entry_v2.asp';

    const PAYMENT_METHOD_ENABLE = 1;
    const PAYMENT_METHOD_DISABLE = 2;

    const PAYMENT_METHOD_CREDIT_BCA = 52;
    const PAYMENT_METHOD_CREDIT_BRI = 35;
    const PAYMENT_METHOD_CREDIT_CIMB = 42;
    const PAYMENT_METHOD_CREDIT_CIMB_AUTH = 56;
    const PAYMENT_METHOD_CREDIT_CIMB_IPG = 34;
    const PAYMENT_METHOD_CREDIT_DANAMON = 45;
    const PAYMENT_METHOD_CREDIT_MANDIRI = 53;
    const PAYMENT_METHOD_CREDIT_MAYBANK = 43;
    const PAYMENT_METHOD_CREDIT_UNIONPAY = 54;
    const PAYMENT_METHOD_CREDIT_UOB = 46;

    const PAYMENT_METHOD_MANDIRI = 4;
    const PAYMENT_METHOD_CIMB_CLICKS = 11;
    const PAYMENT_METHOD_IB_MUAMALAT = 14;
    const PAYMENT_METHOD_DANAMON_ONLINE = 23;

    const PAYMENT_METHOD_ATM_MAYBANK = 9;
    const PAYMENT_METHOD_ATM_MANDIRI = 17;
    const PAYMENT_METHOD_ATM_BCA = 25;
    const PAYMENT_METHOD_ATM_BRI = 26;
    const PAYMENT_METHOD_ATM_PERMATA = 31;

    const PAYMENT_METHOD_XL_TUNAI = 7;
    const PAYMENT_METHOD_MANDIRI_ECASH = 13;
    const PAYMENT_METHOD_TCASH = 15;
    const PAYMENT_METHOD_PAYPRO = 16;
    const PAYMENT_METHOD_OVO = 63;

    const PAYMENT_METHOD_PAYQR = 19;
    const PAYMENT_METHOD_PAY4ME = 22;
    const PAYMENT_METHOD_KREDIVO = 55;
    const PAYMENT_METHOD_ALFAMART = 60;

    const PAYMENT_METHOD_CREDIT_BCA_CONFIG_KEY = 'ipay88_credit_card_bca_bank_config';
    const PAYMENT_METHOD_CREDIT_BRI_CONFIG_KEY = 'ipay88_credit_card_bri_bank_config';
    const PAYMENT_METHOD_CREDIT_CIMB_CONFIG_KEY = 'ipay88_credit_card_cimb_bank_config';
    const PAYMENT_METHOD_CREDIT_CIMB_AUTH_CONFIG_KEY = 'ipay88_credit_card_cimb_auth_bank_config';
    const PAYMENT_METHOD_CREDIT_CIMB_IPG_CONFIG_KEY = 'ipay88_credit_card_cimb_ipg_bank_config';
    const PAYMENT_METHOD_CREDIT_DANAMON_CONFIG_KEY = 'ipay88_credit_card_danamon_bank_config';
    const PAYMENT_METHOD_CREDIT_MANDIRI_CONFIG_KEY = 'ipay88_credit_card_mandiri_bank_config';
    const PAYMENT_METHOD_CREDIT_MAYBANK_CONFIG_KEY = 'ipay88_credit_card_maybank_bank_config';
    const PAYMENT_METHOD_CREDIT_UNIONPAY_CONFIG_KEY = 'ipay88_credit_card_unionpay_bank_config';
    const PAYMENT_METHOD_CREDIT_UOB_CONFIG_KEY = 'ipay88_credit_card_uob_bank_config';

    const PAYMENT_METHOD_CIMB_CLICKS_CONFIG_KEY = 'ipay88_cimb_click_bank_config';
    const PAYMENT_METHOD_IB_MUAMALAT_CONFIG_KEY = 'ipay88_ib_muamalat_bank_config';
    const PAYMENT_METHOD_DANAMON_ONLINE_CONFIG_KEY = 'ipay88_danamon_online_bank_config';

    const PAYMENT_METHOD_ATM_MAYBANK_CONFIG_KEY = 'ipay88_atm_maybank_bank_config';
    const PAYMENT_METHOD_ATM_MANDIRI_CONFIG_KEY = 'ipay88_atm_mandiri_bank_config';
    const PAYMENT_METHOD_ATM_BCA_CONFIG_KEY = 'ipay88_atm_bca_bank_config';
    const PAYMENT_METHOD_ATM_BRI_CONFIG_KEY = 'ipay88_atm_bni_bank_config';
    const PAYMENT_METHOD_ATM_PERMATA_CONFIG_KEY = 'ipay88_atm_permata_bank_config';

    const PAYMENT_METHOD_OVO_CONFIG_KEY = 'ipay88_ovo_bank_config';

    const PAYMENT_METHOD_PAYQR_CONFIG_KEY = 'ipay88_payqr_bank_config';
    const PAYMENT_METHOD_PAY4ME_CONFIG_KEY = 'ipay88_pay4me_bank_config';
    const PAYMENT_METHOD_KREDIVO_CONFIG_KEY = 'ipay88_kredivo_bank_config';
    const PAYMENT_METHOD_ALFAMART_CONFIG_KEY = 'ipay88_alfamart_bank_config';

    protected $paymentMethod;

    protected $banks;

    protected $bankKeyConfig;

    protected $paymentId;

    protected $bankName;

    protected $logo;

    protected $imagePath;

    protected $bankEnabled;

    /**
     * @return array
     */
    public function getBankEnabled()
    {
        return $this->bankEnabled;
    }

    /**
     * @param mixed $bankEnabled
     */
    public function setBankEnabled($bankEnabled)
    {
        $this->bankEnabled = $bankEnabled;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param mixed $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return mixed
     */
    public function getBanks()
    {
        return $this->banks;
    }

    /**
     * @param mixed $banks
     */
    public function setBanks($banks)
    {
        $this->banks = $banks;
    }

    /**
     * @return mixed
     */
    public function getBankKeyConfig()
    {
        return $this->bankKeyConfig;
    }

    /**
     * @param mixed $bankKeyConfig
     */
    public function setBankKeyConfig($bankKeyConfig)
    {
        $this->bankKeyConfig = $bankKeyConfig;
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
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @param mixed $bankName
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @param mixed $imagePath
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    // Payment methods, please view technical spec for latest update.
    protected $pMethod = array(
        self::PAYMENT_METHOD_CREDIT_BCA_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CREDIT_BCA, 'name' => 'Credit Card (BCA)', 'logo' => 'ccbca_s.png'),
        self::PAYMENT_METHOD_CREDIT_BRI_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CREDIT_BRI, 'name' => 'Credit Card (BRI)', 'logo' => 'ccbri_s.png'),
        self::PAYMENT_METHOD_CREDIT_CIMB_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CREDIT_CIMB, 'name' => 'Credit Card (CIMB)', 'logo' => 'cccimb_s.png'),
        self::PAYMENT_METHOD_CREDIT_CIMB_AUTH_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CREDIT_CIMB_AUTH, 'name' => 'Credit Card (CIMB Authorization)', 'logo' => 'cccimbauth_s.png'),
        self::PAYMENT_METHOD_CREDIT_CIMB_IPG_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CREDIT_CIMB_IPG, 'name' => 'Credit Card (CIMB IPG)', 'logo' => 'cccimbipg_s.png'),
        self::PAYMENT_METHOD_CREDIT_DANAMON_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CREDIT_DANAMON, 'name' => 'Credit Card (Danamon)', 'logo' => 'ccdanamon_s.png'),
        self::PAYMENT_METHOD_CREDIT_MANDIRI_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CREDIT_MANDIRI, 'name' => 'Credit Card (Mandiri)', 'logo' => 'ccmandiri_s.png'),
        self::PAYMENT_METHOD_CREDIT_MAYBANK_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CREDIT_MAYBANK, 'name' => 'Credit Card (Maybank)', 'logo' => 'ccmaybank_s.png'),
        self::PAYMENT_METHOD_CREDIT_UNIONPAY_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CREDIT_UNIONPAY, 'name' => 'Credit Card (UnionPay)', 'logo' => 'ccunionpay_s.png'),
        self::PAYMENT_METHOD_CREDIT_UOB_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CREDIT_UOB, 'name' => 'Credit Card (UOB)', 'logo' => 'ccuob_s.png'),

        self::PAYMENT_METHOD_MANDIRI_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_MANDIRI, 'name' => 'Mandiri Clickpay', 'logo' => 'olmandiri_s.png'),
        self::PAYMENT_METHOD_CIMB_CLICKS_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_CIMB_CLICKS, 'name' => 'CIMB Clicks', 'logo' => 'olcimb_s.png'),
        self::PAYMENT_METHOD_IB_MUAMALAT_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_IB_MUAMALAT, 'name' => 'Muamalat IB', 'logo' => 'olmuamalat_s.png'),
        self::PAYMENT_METHOD_DANAMON_ONLINE_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_DANAMON_ONLINE, 'name' => 'Danamon Online Banking', 'logo' => 'oldanamon_s.png'),

        self::PAYMENT_METHOD_ATM_MAYBANK_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_ATM_MAYBANK, 'name' => 'Maybank VA', 'logo' => 'atmmaybank_s.png'),
        self::PAYMENT_METHOD_ATM_MANDIRI_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_ATM_MANDIRI, 'name' => 'Mandiri ATM', 'logo' => 'atmmandiri_s.png'),
        self::PAYMENT_METHOD_ATM_BCA_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_ATM_BCA, 'name' => 'BCA VA', 'logo' => 'atmbca_s.png'),
        self::PAYMENT_METHOD_ATM_BRI_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_ATM_BRI, 'name' => 'BNI VA', 'logo' => 'atmbni_s.png'),
        self::PAYMENT_METHOD_ATM_PERMATA_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_ATM_PERMATA, 'name' => 'Permata VA', 'logo' => 'atmpermata_s.png'),

        self::PAYMENT_METHOD_XL_TUNAI_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_XL_TUNAI, 'name' => 'XL Tunai', 'logo' => 'ewxl_s.png'),
        self::PAYMENT_METHOD_MANDIRI_ECASH_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_MANDIRI_ECASH, 'name' => 'Mandiri e-Cash', 'logo' => 'ewmandiri_s.png'),
        self::PAYMENT_METHOD_TCASH_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_TCASH, 'name' => 'T-Cash', 'logo' => 'ewtcash_s.png'),
        self::PAYMENT_METHOD_PAYPRO_CONFIG_KEY =>  array('paymentId' => self::PAYMENT_METHOD_PAYPRO, 'name' => 'PayPro', 'logo' => 'ewpaypro_s.png'),
        self::PAYMENT_METHOD_OVO_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_OVO, 'name' => 'OVO', 'logo' => 'ewovo_s.png'),

        self::PAYMENT_METHOD_PAYQR_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_PAYQR, 'name' => 'Pay by QR', 'logo' => 'opayqr_s.png'),
        self::PAYMENT_METHOD_PAY4ME_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_PAY4ME, 'name' => 'Pay4ME', 'logo' => 'opay4me_s.png'),
        self::PAYMENT_METHOD_KREDIVO_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_KREDIVO, 'name' => 'Kredivo', 'logo' => 'okredivo_s.png'),
        self::PAYMENT_METHOD_ALFAMART_CONFIG_KEY => array('paymentId' => self::PAYMENT_METHOD_ALFAMART, 'name' => 'Alfamart', 'logo' => 'oalfamart_s.png'),
    );

    /**
     * @return array
     */
    public function getPmethod() {
        return $this->pMethod;
    }

    // Payment methods, please view technical spec for latest update.
    public $paymentMethods = array(
        self::PAYMENT_METHOD_CREDIT_BCA_CONFIG_KEY  => array('name' => 'Credit Card (BCA)', 'logo' => 'ccbca_s.png'),
        self::PAYMENT_METHOD_CREDIT_BRI_CONFIG_KEY  => array('name' => 'Credit Card (BRI)', 'logo' => 'ccbri_s.png'),
        self::PAYMENT_METHOD_CREDIT_CIMB_CONFIG_KEY  => array('name' => 'Credit Card (CIMB)', 'logo' => 'cccimb_s.png'),
        self::PAYMENT_METHOD_CREDIT_CIMB_AUTH_CONFIG_KEY => array('name' => 'Credit Card (CIMB Authorization)', 'logo' => 'cccimbauth_s.png'),
        self::PAYMENT_METHOD_CREDIT_CIMB_IPG_CONFIG_KEY => array('name' => 'Credit Card (CIMB IPG)', 'logo' => 'cccimbipg_s.png'),
        self::PAYMENT_METHOD_CREDIT_DANAMON_CONFIG_KEY => array('name' => 'Credit Card (Danamon)', 'logo' => 'ccdanamon_s.png'),
        self::PAYMENT_METHOD_CREDIT_MANDIRI_CONFIG_KEY => array('name' => 'Credit Card (Mandiri)', 'logo' => 'ccmandiri_s.png'),
        self::PAYMENT_METHOD_CREDIT_MAYBANK_CONFIG_KEY => array('name' => 'Credit Card (Maybank)', 'logo' => 'ccmaybank_s.png'),
        self::PAYMENT_METHOD_CREDIT_UNIONPAY_CONFIG_KEY => array('name' => 'Credit Card (UnionPay)', 'logo' => 'ccunionpay_s.png'),
        self::PAYMENT_METHOD_CREDIT_UOB_CONFIG_KEY => array('name' => 'Credit Card (UOB)', 'logo' => 'ccuob_s.png'),

        self::PAYMENT_METHOD_MANDIRI_CONFIG_KEY => array('name' => 'Mandiri Clickpay', 'logo' => 'olmandiri_s.png'),
        self::PAYMENT_METHOD_CIMB_CLICKS_CONFIG_KEY => array('name' => 'CIMB Clicks', 'logo' => 'olcimb_s.png'),
        self::PAYMENT_METHOD_IB_MUAMALAT_CONFIG_KEY => array('name' => 'Muamalat IB', 'logo' => 'olmuamalat_s.png'),
        self::PAYMENT_METHOD_DANAMON_ONLINE_CONFIG_KEY => array('name' => 'Danamon Online Banking', 'logo' => 'oldanamon_s.png'),

        self::PAYMENT_METHOD_ATM_MAYBANK_CONFIG_KEY => array('name' => 'Maybank VA', 'logo' => 'atmmaybank_s.png'),
        self::PAYMENT_METHOD_ATM_MANDIRI_CONFIG_KEY => array('name' => 'Mandiri ATM', 'logo' => 'atmmandiri_s.png'),
        self::PAYMENT_METHOD_ATM_BCA_CONFIG_KEY => array('name' => 'BCA VA', 'logo' => 'atmbca_s.png'),
        self::PAYMENT_METHOD_ATM_BRI_CONFIG_KEY => array('name' => 'BNI VA', 'logo' => 'atmbni_s.png'),
        self::PAYMENT_METHOD_ATM_PERMATA_CONFIG_KEY => array('name' => 'Permata VA', 'logo' => 'atmpermata_s.png'),

        self::PAYMENT_METHOD_XL_TUNAI_CONFIG_KEY => array('name' => 'XL Tunai', 'logo' => 'ewxl_s.png'),
        self::PAYMENT_METHOD_MANDIRI_ECASH_CONFIG_KEY => array('name' => 'Mandiri e-Cash', 'logo' => 'ewmandiri_s.png'),
        self::PAYMENT_METHOD_TCASH_CONFIG_KEY => array('name' => 'T-Cash', 'logo' => 'ewtcash_s.png'),
        self::PAYMENT_METHOD_PAYPRO_CONFIG_KEY => array('name' => 'PayPro', 'logo' => 'ewpaypro_s.png'),
        self::PAYMENT_METHOD_OVO_CONFIG_KEY => array('name' => 'OVO', 'logo' => 'ewovo_s.png'),

        self::PAYMENT_METHOD_PAYQR_CONFIG_KEY => array('name' => 'Pay by QR', 'logo' => 'opayqr_s.png'),
        self::PAYMENT_METHOD_PAY4ME_CONFIG_KEY => array('name' => 'Pay4ME', 'logo' => 'opay4me_s.png'),
        self::PAYMENT_METHOD_KREDIVO_CONFIG_KEY => array('name' => 'Kredivo', 'logo' => 'okredivo_s.png'),
        self::PAYMENT_METHOD_ALFAMART_CONFIG_KEY => array('name' => 'Alfamart', 'logo' => 'oalfamart_s.png'),
    );

    /**
     * @param $key
     * @return null
     */
    public function getPaymentMethodInfoByKey($key) {
        if(isset($this->pMethod[$key])) {
            return $this->pMethod[$key];
        }
        return null;
    }

    /**
     * @param $id
     * @return null
     */
    public function getPaymentMethodInfoById($id) {
        if(isset($this->pMethod[$id])) {
            return $this->pMethod[$id];
        }
        return null;
    }

    public function __construct($options = null)
    {
    }


    public function getWiget() {
        $html = '';

        $bankEnable = $this->getBankEnabled();

        if(is_array($bankEnable) && count($bankEnable)) {
            foreach ($bankEnable as $bank) {

            }
        }

        return $html;
    }
}