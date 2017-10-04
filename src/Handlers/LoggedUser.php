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
 * Class LoggedUser
 * @package Bywciu\Filmweb\Handlers
 */
class LoggedUser extends BasicHandler
{
    /**
     * IsLoggedUser constructor.
     */
    public function __construct()
    {
        $this->methodName = 'isLoggedUser';
        $this->responseKeys = [
            'username', 'avatar', 'name', 'userId', 'gender'
        ];

        parent::__construct();
    }

    /**
     * Make request
     *
     * @return array
     * @throws ApiException
     */
    public function getData()
    {
        try {
            $methodString = $this->getMethodString();
            $clientResponse = $this->client->request('get', '', [
                'query' => $this->prepareRequest($methodString)
            ]);

            return $this->mapper($this->parseResponse($clientResponse->getBody()->getContents()), $this->responseKeys);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Map fields with response
     *
     * @param array $response
     * @param array $fields
     *
     * @return array
     * @throws ApiException
     */
    public function mapper($response, $fields = []) {
        if (empty($response)) {
            throw new ApiException('No content received', 204);
        }

        $return = [];
        foreach ($response as $i => $item) {
            // avatar
            if ($i === 1 && !empty($item)) {
                $return[!empty($fields[$i]) ? $fields[$i] : $i] = $this->assetsUrls['userImageUrl'] . $item;
            }
            else {
                $return[!empty($fields[$i]) ? $fields[$i] : $i] = $item;
            }
        }

        return $return;
    }
}
