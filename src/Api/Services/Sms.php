<?php

namespace Coreproc\Globe\Labs\Api\Services;

use Coreproc\Globe\Labs\Api\GlobeLabsService;
use Coreproc\MsisdnPh\Msisdn;
use GuzzleHttp\Client;

class Sms extends GlobeLabsService
{

    /**
     * @var string
     */
    private $accessToken = null;

    /**
     * @var string
     */
    private $message = null;

    /**
     * @var mixed
     */
    private $clientCorrelator = null;

    /**
     * @var string
     */
    private $mobileNumber = null;

    /**
     * @var Msisdn
     */
    private $msisdn = null;

    /**
     * Base url of the API
     *
     * @var string
     */
    private $baseUrl = 'http://devapi.globelabs.com.ph/smsmessaging/v1/outbound/{senderAddress}/requests?access_token={access_token}';

    /**
     * @param null $accessToken
     * @param null $mobileNumber is the subscriber MSISDN (mobile number)
     * @param null $message
     * @param null $clientCorrelator
     * @return bool Sent or not
     */
    public function send($accessToken = null, $mobileNumber = null, $message = null, $clientCorrelator = null)
    {
        if ( ! empty($accessToken)) $this->accessToken = $accessToken;
        if ( ! empty($mobileNumber)) $this->mobileNumber = $mobileNumber;
        if ( ! empty($message)) $this->message = $message;
        if ( ! empty($clientCorrelator)) $this->clientCorrelator = $clientCorrelator;

        $this->msisdn = new Msisdn($this->mobileNumber);
        if ( ! $this->msisdn->isValid()) {
            return false;
        }

        $data = [
            'outboundSMSMessageRequest' => [
                'clientCorrelator'       => $this->clientCorrelator,
                'senderAddress'          => 'tel:' . substr($this->msisdn->get(), -4),
                'outboundSMSTextMessage' => [
                    'message' => $this->message
                ],
                'address'                => [
                    'tel:+' . $this->msisdn->get(true)
                ]
            ]
        ];

        try {
            $client = new Client();
            $response = $client->post($this->buildUrl(), [
                'body'    => json_encode($data),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            if ($response->getStatusCode() != 201) {
                return false;
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            die($e->getMessage());
            return false;
        }

        return true;
    }

    private function buildUrl()
    {
        $mobileNumber = $this->msisdn->get();

        $lastFourDigits = substr($mobileNumber, -4);

        $url = str_replace('{senderAddress}', $lastFourDigits, $this->baseUrl);
        $url = str_replace('{access_token}', $this->accessToken, $url);

        return $url;
    }

}