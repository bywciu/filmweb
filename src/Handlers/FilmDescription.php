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
 * Class FilmDescription
 * @package Bywciu\Filmweb\Handlers
 */
class FilmDescription extends BasicHandler
{
    /**
     * FilmDescription constructor.
     */
    public function __construct()
    {
        $this->methodName = 'getFilmDescription';
        $this->responseKeys = [
            'description'
        ];

        parent::__construct();
    }

    /**
     * Make request
     *
     * @param int $filmId
     * @return array
     * @throws ApiException
     */
    public function getData($filmId)
    {
        try {
            $methodString = $this->getMethodString($filmId);
            $clientResponse = $this->client->request('get', '', [
                'query' => $this->prepareRequest($methodString)
            ]);

            return $this->mapper($this->parseResponse($clientResponse->getBody()->getContents()), $this->responseKeys);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }
}
