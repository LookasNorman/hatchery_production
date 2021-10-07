<?php

namespace App\SMS;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;
use Smsapi\Client\Feature\Sms\Data\Sms;
use Smsapi\Client\Curl\SmsapiHttpClient;

class SMSSend
{

    /**
     * @Route("/sms", methods={"GET"})
     */
    public function sendSms()
    {
        $sms = (new SmsapiHttpClient())
            ->smsapiPlService('%env(SMS_TOKEN)')
            ->smsFeature()
            ->sendSms(SendSmsBag::withMessage('48669905464', 'Hello world!'));

        var_dump($sms);
    }
}
