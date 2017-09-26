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
 * Class FilmsInfoShort
 * @package Bywciu\Filmweb\Handlers
 */
class FilmsInfoShort extends BasicHandler
{
    /**
     * FilmsInfoShort constructor.
     */
    public function __construct()
    {
        $this->methodName = 'getFilmsInfoShort';
        $this->responseKeys = [
            'title', 'year', 'avgRate', 'votesCount', 'duration', 'imagePath'
        ];

        parent::__construct();
    }

    /**
     * Make request
     *
     * @param array $filmIds
     * @return array
     * @throws ApiException
     */
    public function getData(array $filmIds)
    {
        try {
            $methodString = $this->getMethodString($filmIds);
            $clientResponse = $this->client->request('get', '', [
                'query' => $this->prepareRequest($methodString)
            ]);

            return $this->parseResponse($clientResponse->getBody()->getContents());
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Return parsed filmweb response
     *
     * @param string $response
     *
     * @return array
     * @throws \Exception
     */
    public function parseResponse($response)
    {
        try {
            $return = [];
            $parsedResponse = parent::parseResponse($response);

            if (!empty($parsedResponse)) {
                foreach ($parsedResponse as $entry) {
                    $return[] = $this->mapper($entry);
                }
            }

            return $return;
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
            // imagePath
            if ($i === 5 && !empty($item)) {
                $return[!empty($this->responseKeys[$i]) ? $this->responseKeys[$i] : $i] = $this->assetsUrls['filmImageUrl'] . $item;
            }
            else {
                $return[!empty($this->responseKeys[$i]) ? $this->responseKeys[$i] : $i] = $item;
            }
        }

        return $return;
    }
}
