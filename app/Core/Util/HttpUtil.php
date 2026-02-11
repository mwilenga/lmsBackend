<?php

namespace App\Core\Util;

use Illuminate\Support\Facades\Log;

class HttpUtil
{

    public static function post($postData)
    {
        $api_key = config('constants.kilakona-sms.api_key');
        $secret_key = config('constants.kilakona-sms.secret_key');
        $apiUrl = config('constants.kilakona-sms.send_sms_url');

        $ch = curl_init($apiUrl);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'api_key: ' . $api_key,
                'api_secret: ' . $secret_key,
                'Content-Type: application/json',
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($ch);

        return $response;
    }
}
