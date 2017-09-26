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
namespace Bywciu\Filmweb\Handlers;

use Bywciu\Filmweb\Common\ApiException;
use Bywciu\Filmweb\Common\BasicHandler;

/**
 * Class Login
 * @package Bywciu\Filmweb\Handlers
 */
class Login extends BasicHandler
{
    /**
     * Login constructor.
     */
    public function __construct()
    {
        $this->methodName = 'login';
        $this->responseKeys = [
            'username', 'avatar', 'name', 'userId', 'gender'
        ];

        parent::__construct();
    }

    /**
     * Make request
     *
     * @param string $user
     * @param string $password
     * @param int $rememberMe
     *
     * @return array
     * @throws ApiException
     */
    public function getData($user, $password, $rememberMe = 1)
    {
        try {
            $methodString = $this->getMethodString($user, $password, $rememberMe);
            $clientResponse = $this->client->request('post', '', [
                'query' => $this->prepareRequest($methodString)
            ]);

            return $this->mapper($this->parseResponse($clientResponse->getBody()->getContents()));
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Map fields with response
     *
     * @param array $response
     *
     * @return array
     * @throws \Exception
     */
    public function mapper($response) {
        if (empty($response)) {
            throw new ApiException('No content received', 204);
        }

        $return = [];
        foreach ($response as $i => $item) {
            // avatar
            if ($i === 1 && !empty($item)) {
                $return[!empty($this->responseKeys[$i]) ? $this->responseKeys[$i] : $i] = $this->assetsUrls['userImageUrl'] . $item;
            }
            else {
                $return[!empty($this->responseKeys[$i]) ? $this->responseKeys[$i] : $i] = $item;
            }
        }

        return $return;
    }
}
