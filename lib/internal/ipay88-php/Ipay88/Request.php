<?php
/**
 * Created by PhpStorm.
 * User: shinichi
 * Date: 11/15/17
 * Time: 9:25 AM
 */
class Ipay88_Request
{
    protected $MerchantCode;

    protected $PaymentId;

    protected $RefNo;

    protected $Amount;

    protected $Currency;

    protected $ProdDesc;

    protected $UserName;

    protected $UserEmail;

    protected $UserContact;

    protected $Remark;

    protected $Lang;

    protected $Signature;

    protected $ResponseURL;

    protected $BackendURL;

    protected $BaseURL;

    protected $title;

    protected $action;

    protected $imagePath;

    protected $data;

    public function __construct($options = null)
    {
        $this->data = $options;
    }

    public function generateRedirectForm($data = null, $auto = false) {

        $action = $this->getAction() ? : '';
        $formName = 'ipay88PaymentRedirectForm';
        $htmlCode =
            '<form style="text-align:center;" name="'.$formName.'"  method="POST" action="'.$action.'">';
                $options = $data ? : $this->getData();
                if(is_array($options) && count($options)) {
                    foreach ($options as $key => $option) {
                        $htmlCode .= '<input type="hidden" name="'.$key.'" value="'.$option.'" />';                     
                    }
                }
                // image
                $loadingImage = $this->getImagePath() ? : '';
                $htmlCode .=
                    '<div align="center" style="width:100%">
                        <p>
                            '.$this->getTitle().'
                        </p>
                        <img src="'.$loadingImage.'" border="0">
                    </div>';
                $htmlCode .=
                    '<input style="background-color: darkorange" type="submit" class="action primary tocart" value="Pay Now" />
            </form>';
            // Auto submit
        $actionFormName = "document.ipay88PaymentRedirectForm.submit()";
        if($formName) {
            $actionFormName = "document.".$formName.".submit()";
        }

        $htmlCode .=
            '<script type="text/javascript">
                setTimeout('.$actionFormName.', 3000);
            </script>';

        return $htmlCode;
    }

    /**
     * @return mixed
     */
    public function getMerchantCode()
    {
        return $this->MerchantCode;
    }

    /**
     * @param mixed $MerchantCode
     */
    public function setMerchantCode($MerchantCode)
    {
        $this->MerchantCode = $MerchantCode;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->PaymentId;
    }

    /**
     * @param mixed $PaymentId
     */
    public function setPaymentId($PaymentId)
    {
        $this->PaymentId = $PaymentId;
    }

    /**
     * @return mixed
     */
    public function getRefNo()
    {
        return $this->RefNo;
    }

    /**
     * @param mixed $RefNo
     */
    public function setRefNo($RefNo)
    {
        $this->RefNo = $RefNo;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->Amount;
    }

    /**
     * @param mixed $Amount
     */
    public function setAmount($Amount)
    {
        $this->Amount = $Amount;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->Currency;
    }

    /**
     * @param mixed $Currency
     */
    public function setCurrency($Currency)
    {
        $this->Currency = $Currency;
    }

    /**
     * @return mixed
     */
    public function getProdDesc()
    {
        return $this->ProdDesc;
    }

    /**
     * @param mixed $ProdDesc
     */
    public function setProdDesc($ProdDesc)
    {
        $this->ProdDesc = $ProdDesc;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->UserName;
    }

    /**
     * @param mixed $UserName
     */
    public function setUserName($UserName)
    {
        $this->UserName = $UserName;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->UserEmail;
    }

    /**
     * @param mixed $UserEmail
     */
    public function setUserEmail($UserEmail)
    {
        $this->UserEmail = $UserEmail;
    }

    /**
     * @return mixed
     */
    public function getUserContact()
    {
        return $this->UserContact;
    }

    /**
     * @param mixed $UserContact
     */
    public function setUserContact($UserContact)
    {
        $this->UserContact = $UserContact;
    }

    /**
     * @return mixed
     */
    public function getRemark()
    {
        return $this->Remark;
    }

    /**
     * @param mixed $Remark
     */
    public function setRemark($Remark)
    {
        $this->Remark = $Remark;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->Lang;
    }

    /**
     * @param mixed $Lang
     */
    public function setLang($Lang)
    {
        $this->Lang = $Lang;
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->Signature;
    }

    /**
     * @param mixed $Signature
     */
    public function setSignature($Signature)
    {
        $this->Signature = $Signature;
    }

    /**
     * @return mixed
     */
    public function getResponseURL()
    {
        return $this->ResponseURL;
    }

    /**
     * @param mixed $ResponseURL
     */
    public function setResponseURL($ResponseURL)
    {
        $this->ResponseURL = $ResponseURL;
    }

    /**
     * @return mixed
     */
    public function getBaseURL()
    {
        return $this->BaseURL;
    }

    /**
     * @param mixed $ResponseURL
     */
    public function setBaseURL($BaseURL)
    {
        $this->BaseURL = $BaseURL;
    }

    /**
     * @return mixed
     */
    public function getBackendURL()
    {
        return $this->BackendURL;
    }

    /**
     * @param mixed $BackendURL
     */
    public function setBackendURL($BackendURL)
    {
        $this->BackendURL = $BackendURL;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
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

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}