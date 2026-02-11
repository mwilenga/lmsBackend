<?php

namespace App\Core\Util;

use Illuminate\Support\Facades\Log;

class SMSUtil
{

    public static function sendSMS($message, $destination)
    {
        $senderID = config('constants.kilakona-sms.sender_ID');
        $postData = array(
            'senderId' => $senderID,
            'messageType' => 'text',
            'message' => $message,
            'contacts' => $destination
        );

        $response = HttpUtil::post($postData);

        return $response;
    }
}
