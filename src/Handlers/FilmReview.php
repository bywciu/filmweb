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
 * Class FilmReview
 * @package Bywciu\Filmweb\Handlers
 */
class FilmReview extends BasicHandler
{
    /**
     * FilmReview constructor.
     */
    public function __construct()
    {
        $this->methodName = 'getFilmReview';
        $this->responseKeys = [
            'authorName', 'authorUserId', 'authorImagePath', 'review', 'title'
        ];

        parent::__construct();
    }

    /**
     * Make request
     *
     * @param int $filmId
     *
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
            // authorImagePath
            if ($i === 2 && !empty($item)) {
                $return[!empty($this->responseKeys[$i]) ? $this->responseKeys[$i] : $i] = $this->assetsUrls['userImageUrl'] . $item;
            }
            else {
                $return[!empty($this->responseKeys[$i]) ? $this->responseKeys[$i] : $i] = $item;
            }
        }

        return $return;
    }
}
