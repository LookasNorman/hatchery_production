<?php

namespace App\Service;

use Smsapi\Client\Curl\SmsapiHttpClient;
use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;

class SendSMS
{
    public function singleSMS($phoneNumber, $message)
    {
        $sms = (new SmsapiHttpClient())
            ->smsapiPlService($this->getParameter('app.smstoken'))
            ->smsFeature()
            ->sendSms(SendSmsBag::withMessage($phoneNumber, $message));
    }
}
