<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model;

/**
 * Class Linkedin
 * @package WeltPixel\SocialLogin\Model
 */
class Linkedin extends \WeltPixel\SocialLogin\Model\Sociallogin
{
    /**
     * @var string
     */
    protected $_type = 'linkedin';
    /**
     * @var string
     */
    protected $_apiTokenUrl = 'https://www.linkedin.com/oauth/v2/accessToken';
    /**
     * @var string
     */
    protected $_apiTokenRequestUrl = 'https://www.linkedin.com/v1/people/~:(id,first-name,last-name,email-address)';


    /**
     * @var array
     */
    protected $_fields = [
        'user_id' => 'id',
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'email' => 'email',
        'gender' => 'gender'
    ];

    public function _construct()
    {
        parent::_construct();
    }

    /**
     * @param $response
     * @return bool
     */
    public function fetchUserData($response)
    {
        if (empty($response)) {
            return false;
        }

        $data = $userData = [];

        $params = [
            'client_id' => $this->_applicationId,
            'client_secret' => $this->_secret,
            'grant_type' =>  'authorization_code',
            'code' => $response,
            'redirect_uri' => $this->_redirectUri
        ];

        $apiToken = false;
        $headerArr = [
            'Authorization: Bearer '. $response
        ];
        if ($response = $this->_apiCall($this->_apiTokenUrl, $params, 'POST', null, $headerArr)) {
            $apiToken = @json_decode($response, true);
            if (!$apiToken) {
                parse_str($response, $apiToken);
            }
            $data = json_decode($response, true);
            if(isset($data['access_token'])){
                $token = $data['access_token'];
                $this->_setToken($token);

                $reqUrl = $this->_apiTokenRequestUrl;
                $this->_setCurlHeader();
                $apiDetails = $this->httpGet($reqUrl);
                $data = simplexml_load_string($apiDetails);
                $data = $this->_object2array($data);
                $this->_setUserData($data);
            }
        }

        if (!$this->_userData = $this->_setSocialUserData($this->_userData)) {
            return false;
        }

        return true;
    }

    /**
     * @param $userData
     */
    protected function _setUserData($userData) {
        $this->_userData['id'] = $userData['id'];
        $this->_userData['email'] = $userData['email-address'];
        $this->_userData['firstname'] = isset($userData['first-name']) ? $userData['first-name'] : self::PROVIDER_FIRSTNAME_PLACEHOLDER;
        $this->_userData['lastname'] = $userData['last-name'] ? $userData['last-name'] : self::PROVIDER_LASTNAME_PLACEHOLDER;
    }

    /**
     * @param $data
     * @return array|bool
     */
    protected function _setSocialUserData($data)
    {
        if (empty($data['id'])) {
            return false;
        }

        return parent::_setSocialUserData($data);
    }

    /**
     * @param $object
     * @return mixed
     */
    protected function _object2array($object) {
        return @json_decode(@json_encode($object),1);
    }


}
