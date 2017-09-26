<?php
/**
 * @author Mateusz Bywalec
 * @copyright (c) 2017 bywciu
 * @description filmweb.pl API Bridge
 * @version 1.1
 * @link http://bywciu.com/
 * @link https://github.com/bywciu/filmweb
 * @license MIT
 */
namespace Bywciu\Filmweb\Common;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;

/**
 * Class BasicHandler
 * @package Bywciu\Filmweb\Common
 */
class BasicHandler
{
    /**
     * @var array
     */
    protected $assetsUrls = [
        'channelImageUrl' => 'http://1.fwcdn.pl/channels',
        'filmImageUrl' => 'http://1.fwcdn.pl/po',
        'filmPhotoUrl' => 'http://1.fwcdn.pl/ph',
        'personImageUrl' => 'http://1.fwcdn.pl/p',
        'userImageUrl' => 'http://1.fwcdn.pl/u',
        'captchaImageUrl' => 'https://ssl.filmweb.pl/captcha/',
    ];

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var array
     */
    protected $responseKeys;

    /**
     * @var string
     */
    private $apiUrl = 'https://ssl.filmweb.pl/api';

    /**
     * @var string
     */
    private $apiKey = 'qjcGhW2JnvGT9dfCt3uT_jozR3s';

    /**
     * @var string
     */
    private $version = '1.0';

    /**
     * @var string
     */
    private $appId = 'android';

    /**
     * BasicHandler constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'cookies' => new FileCookieJar(
                dirname(__FILE__) .
                DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . 'cookies.txt'),
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
    }

    /**
     * Prepare filmweb request
     *
     * @param $methodString
     * @return array
     */
    public function prepareRequest($methodString)
    {
        return [
            'methods' => $methodString,
            'signature' => '1.0,' . md5($methodString . 'android' . $this->apiKey),
            'version' => $this->version,
            'appId' => $this->appId
        ];
    }

    /**
     * Return parsed filmweb response
     *
     * @param string $response
     * @return array
     * @throws \Exception
     */
    public function parseResponse($response)
    {
        try {
            $clientResponse = str_getcsv($response, "\n");

            if ($clientResponse[0] !== 'ok') {
                throw new ApiException(join(' ', $clientResponse));
            }

            $parsedResponse = json_decode(preg_replace(['/ t:[0-9]+/is', '/ s/i'], '', $clientResponse[1]));

            // There is no data in response
            if ($parsedResponse == 'exc NullPointerException') {
                throw new ApiException('No content received', 204);
            }

            return $parsedResponse;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Return methodsString
     *
     * @return string
     */
    public function getMethodString()
    {
        $return = [];
        $args = func_get_args();

        if (!empty($args)) {
            foreach ($args as $arg) {
                if (is_array($arg)) {
                    $retValue = [];
                    foreach($arg as $value) {
                        if (!is_numeric($value)) {
                            $retValue[] = '"' . $value . '"';
                        } else {
                            $retValue[] = $value;
                        }
                    }

                    $return[] = '[' . join(',', $retValue) . ']';
                }
                elseif (!is_numeric($arg)) {
                    $return[] = '"' . $arg . '"';
                }
                else {
                    $return[] = $arg;
                }
            }
        }

        return $this->methodName . ' [' . join(',', $return) . ']\n';
    }

    /**
     * Map fields with response
     *
     * @param array $response
     * @param array $fields
     *
     * @return array
     * @throws \Exception
     */
    public function mapper($response, $fields = []) {
        if (empty($response)) {
            throw new ApiException('No fields to map', 204);
        }

        $return = [];
        foreach ($response as $i => $item) {
            $return[!empty($fields[$i]) ? $fields[$i] : $i] = $item;
        }

        return $return;
    }
}
