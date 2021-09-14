<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    SocialLogin
 * @version    1.6.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\SocialLogin\Model\Provider\Account\Retriever;

use Aheadworks\SocialLogin\Model\Provider\Account\AbstractRetriever;
use Aheadworks\SocialLogin\Model\Provider\AccountInterface;
use Aheadworks\SocialLogin\Model\Provider\Service\ServiceInterface;

class Vk extends AbstractRetriever
{
    /**
     * Get account method
     */
    const API_METHOD_USERS_GET = 'users.get';

    /**
     * @var array
     */
    private $requestParams = [
        'fields' => 'photo_50',
        'v' => '5.107'
    ];

    /**
     * {@inheritdoc}
     */
    protected function requestData(ServiceInterface $service)
    {
        /** @var \Aheadworks\SocialLogin\Model\Provider\Service\Vk $service */
        $response = $service->requestWithParams(self::API_METHOD_USERS_GET, $this->requestParams);
        $responseData = $this->decodeJson($response);

        return $this->createDataObject()->setData($responseData);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareResponseData(\Magento\Framework\DataObject $responseData)
    {
        return [
            AccountInterface::TYPE => AccountInterface::TYPE_VK,
            AccountInterface::SOCIAL_ID => $responseData->getData('response/0/id'),
            AccountInterface::FIRST_NAME => $responseData->getData('response/0/first_name'),
            AccountInterface::LAST_NAME => $responseData->getData('response/0/last_name'),
            AccountInterface::IMAGE_URL => $responseData->getData('response/0/photo_50')
        ];
    }
}
