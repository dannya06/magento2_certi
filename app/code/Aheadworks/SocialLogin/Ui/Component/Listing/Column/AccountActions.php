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
namespace Aheadworks\SocialLogin\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class AccountActions
 */
class AccountActions extends Column
{
    const URL_BACK_SOCIAL_PARAM_VALUE = 'social';

    /**
     * Url path
     */
    const URL_PATH_CUSTOMER_EDIT = 'customer/index/edit';

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['customer_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->getContext()->getUrl(
                                static::URL_PATH_CUSTOMER_EDIT,
                                [
                                    'id' => $item['customer_id'],
                                    'back' => self::URL_BACK_SOCIAL_PARAM_VALUE
                                ]
                            ),
                            'label' => __('View customer')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
