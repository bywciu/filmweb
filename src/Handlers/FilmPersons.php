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
 * Class FilmPersons
 * @package Bywciu\Filmweb\Handlers
 *
 * @see $types = [
 *      1 => 'scenarzysta',
 *      2 => 'reżyser',
 *      3 => 'zdjęcia',
 *      4 => 'muzyka',
 *      5 => 'scenografia',
 *      6 => 'aktor',
 *      7 => 'producent',
 *      10 => 'montaż',
 *      13 => 'kostiumy',
 *      17 => 'materiały do scenariusza',
 *      18 => 'dźwięk',
 *      19 => 'materiały archiwalne',
 *      20 => 'głos',
 *      21 => 'we własnej osobie'
 *   ]
 */
class FilmPersons extends BasicHandler
{
    /**
     * FilmPersons constructor.
     */
    public function __construct()
    {
        $this->methodName = 'getFilmPersons';
        $this->responseKeys = [
            'personId', 'assocName', 'assocAttributes', 'personName', 'personImagePath'
        ];

        parent::__construct();
    }

    /**
     * Make request
     *
     * @param int $filmId
     * @param int $type
     * @param int $page
     * @param int $limit
     *
     * @return array
     * @throws ApiException
     */
    public function getData($filmId, $type, $page = 0, $limit = 50)
    {
        try {
            $methodString = $this->getMethodString($filmId, $type, $page, $limit);
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
            // personImagePath
            if ($i === 4 && !empty($item)) {
                $return[!empty($fields[$i]) ? $fields[$i] : $i] = $this->assetsUrls['personImageUrl'] . $item;
            }
            else {
                $return[!empty($fields[$i]) ? $fields[$i] : $i] = $item;
            }
        }

        return $return;
    }
}
