<?php
/*
 *  Last Modified: 6/29/21, 12:06 AM
 *  Copyright (c) 2021
 *  -created by Ariful Islam
 *  -All Rights Preserved By
 *  -If you have any query then knock me at
 *  arif98741@gmail.com
 *  See my profile @ https://github.com/arif98741
 */

namespace Xenon\LaravelBDSms\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Xenon\LaravelBDSms\Handler\ParameterException;
use Xenon\LaravelBDSms\Sender;

/**
 * Class MimSms
 * @package Xenon\LaravelBDSms\Provider
 * @version v1.0.20
 * @since v1.0.20
 */
class MimSms extends AbstractProvider
{
    /**
     * Mimsms constructor.
     * @param Sender $sender
     * @version v1.0.20
     * @since v1.0.20
     */
    public function __construct(Sender $sender)
    {
        $this->senderObject = $sender;
    }

    /**
     * Send Request To Api and Send Message
     * @throws GuzzleException
     * @version v1.0.20
     * @since v1.0.20
     */
    public function sendRequest()
    {
        $number = $this->senderObject->getMobile();
        $text = $this->senderObject->getMessage();
        $config = $this->senderObject->getConfig();

        $client = new Client([
            'base_uri' => 'https://esms.mimsms.com/smsapi',
            'timeout' => 10.0,
            'verify' => false
        ]);

        $response = $client->request('GET', '', [
            'query' => [
                'api_key' => $config['api_key'],
                'type' => $config['type'],
                'senderid' => $config['senderid'],
                'contacts' => $number,
                'msg' => $text,
            ]
        ]);
        $body = $response->getBody();
        $smsResult = $body->getContents();

        $data['number'] = $number;
        $data['message'] = $text;
        $report = $this->generateReport($smsResult, $data);
        return $report->getContent();
    }

    /**
     * @throws ParameterException
     * @version v1.0.20
     * @since v1.0.20
     */
    public function errorException()
    {
        if (!array_key_exists('api_key', $this->senderObject->getConfig())) {
            throw new ParameterException('api_key is absent in configuration');
        }
        if (!array_key_exists('type', $this->senderObject->getConfig())) {
            throw new ParameterException('type key is absent in configuration');
        }
        if (!array_key_exists('senderid', $this->senderObject->getConfig())) {
            throw new ParameterException('senderid key is absent in configuration');
        }
    }

}
