<?php
/**
 * @author Mateusz Bywalec
 * @copyright (c) 2017 bywciu
 * @description filmweb.pl API Bridge
 * @version 1.1.5
 * @link http://bywciu.com/
 * @link https://github.com/bywciu/filmweb
 * @license MIT
 */
namespace Bywciu\Filmweb\Handlers;

use Bywciu\Filmweb\Common\ApiException;
use Bywciu\Filmweb\Common\BasicHandler;

/**
 * Class FilmImages
 * @package Bywciu\Filmweb\Handlers
 */
class FilmImages extends BasicHandler
{
    /**
     * FilmImages constructor.
     */
    public function __construct()
    {
        $this->methodName = 'getFilmImages';
        $this->responseKeys = [
            'imagePath', 'persons', 'photoSources'
        ];

        parent::__construct();
    }

    /**
     * Make request
     *
     * @param int $filmId
     * @param int $page
     * @param int $limit
     *
     * @return array
     * @throws ApiException
     */
    public function getData($filmId, $page = 0, $limit = 100)
    {
        try {
            $methodString = $this->getMethodString($filmId, $page, $limit);
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
                    $return[] = $this->mapper($entry, $this->responseKeys);
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
            // imagePath
            if ($i === 0 && !empty($item)) {
                $return[!empty($fields[$i]) ? $fields[$i] : $i] = $this->assetsUrls['filmPhotoUrl'] . $item;
            }
            else {
                $return[!empty($fields[$i]) ? $fields[$i] : $i] = $item;
            }
        }

        return $return;
    }
}
