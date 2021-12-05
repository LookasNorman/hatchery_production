<?php

namespace App\Service;

use Smsapi\Client\Curl\SmsapiHttpClient;
use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SendSMS
{
    private $smstoken;

    public function __construct($smstoken)
    {
        $this->smstoken = $smstoken;
    }

    public function singleSMS($phoneNumber, $message)
    {
        $sms = (new SmsapiHttpClient())
            ->smsapiPlService($this->smstoken)
            ->smsFeature()
            ->sendSms(SendSmsBag::withMessage($phoneNumber, $message));
    }
}
