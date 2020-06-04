<?php

namespace Midtrans;

/**
 * Provide charge and capture functions for Core API
 */
class CoreApi
{
    /**
     * Create transaction.
     *
     * @param mixed[] $params Transaction options
     */
    public static function charge($params)
    {
        $payloads = array(
            'payment_type' => 'credit_card'
        );

        if (array_key_exists('item_details', $params)) {
            $gross_amount = 0;
            foreach ($params['item_details'] as $item) {
                $gross_amount += $item['quantity'] * $item['price'];
            }
            $payloads['transaction_details']['gross_amount'] = $gross_amount;
        }

        $payloads = array_replace_recursive($payloads, $params);

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $apiRequestor = $om->get('Midtrans\ApiRequestor');
        $config = $om->get('Midtrans\Config');
        $san = $om->get('Midtrans\Sanitizer');

        if ($config->getIsSanitized()) {
            $san->jsonRequest($payloads);
        }

        $result = $apiRequestor->post(
            $config->getBaseUrl() . '/charge',
            $config->getServerKey(),
            $payloads
        );

        return $result;
    }

    /**
     * Capture pre-authorized transaction
     *
     * @param string $param Order ID or transaction ID, that you want to capture
     */
    public static function capture($param, $amount = "")
    {
        $payloads = array(
            'transaction_id' => $param,
        );

        if ($amount != "") {
            $payloads = array(
                'transaction_id' => $param,
                'gross_amount' => $amount
            );
        }

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $apiRequestor = $om->get('Midtrans\ApiRequestor');
        $config = $om->get('Midtrans\Config');

        $result = $apiRequestor->post(
            $config->getBaseUrl() . '/capture',
            $config->getServerKey(),
            $payloads
        );

        return $result;
    }
}