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
namespace Bywciu\Filmweb;

use Bywciu\Filmweb\Common\ApiException;
use Bywciu\Filmweb\Handlers\FilmDescription;
use Bywciu\Filmweb\Handlers\FilmImages;
use Bywciu\Filmweb\Handlers\FilmInfoFull;
use Bywciu\Filmweb\Handlers\FilmPersons;
use Bywciu\Filmweb\Handlers\FilmReview;
use Bywciu\Filmweb\Handlers\FilmsInfoShort;
use Bywciu\Filmweb\Handlers\FilmVideos;
use Bywciu\Filmweb\Handlers\LoggedUser;
use Bywciu\Filmweb\Handlers\Login;
use Bywciu\Filmweb\Handlers\UserFilmsWantToSee;
use Bywciu\Filmweb\Handlers\UserFilmVotes;

/**
 * Class Api
 * @package Bywciu\Filmweb
 */
class Api
{
    /**
     * @var array
     */
    public $user;

    /**
     * Api constructor.
     *
     * @param string $user
     * @param string $password
     * @param int $rememberMe
     */
    public function __construct($user, $password, $rememberMe = 1)
    {
        try {
            $this->user = $this->isLoggedUser();
        }
        catch (ApiException $e) {
            $this->user = $this->login($user, $password, $rememberMe);
        }
    }

    /**
     * Login user
     *
     * @param string $user
     * @param string $password
     * @param int $rememberMe
     *
     * @return array
     */
    public function login($user, $password, $rememberMe = 1)
    {
        $login = new Login();
        return $login->getData($user, $password, $rememberMe);
    }

    /**
     * Check if user is logged in
     *
     * @return array
     */
    public function isLoggedUser()
    {
        $loggedUser = new LoggedUser();
        return $loggedUser->getData();
    }

    /**
     * Get movie description
     *
     * @param int $filmId
     *
     * @return array
     */
    public function getFilmDescription($filmId)
    {
        $filmDescription = new FilmDescription();
        return $filmDescription->getData($filmId);
    }

    /**
     * Get movie images
     *
     * @param int $filmId
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getFilmImages($filmId, $page = 0, $limit = 100)
    {
        $filmImages = new FilmImages();
        return $filmImages->getData($filmId, $page, $limit);
    }

    /**
     * Get full movie info
     *
     * @param $filmId
     *
     * @return array
     */
    public function getFilmInfoFull($filmId)
    {
        $filmFullInfo = new FilmInfoFull();
        return $filmFullInfo->getData($filmId);
    }

    /**
     * Get movie persons
     *
     * @param int $filmId
     * @param int $type
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getFilmPersons($filmId, $type, $page = 0, $limit = 50)
    {
        $filmPersons = new FilmPersons();
        return $filmPersons->getData($filmId, $type, $page, $limit);
    }

    /**
     * Get movie review
     *
     * @param int $filmId
     *
     * @return array
     */
    public function getFilmReview($filmId)
    {
        $filmReview = new FilmReview();
        return $filmReview->getData($filmId);
    }

    /**
     * Get movie videos
     *
     * @param int $filmId
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getFilmVideos($filmId, $page = 0, $limit = 100)
    {
        $filmVideos = new FilmVideos();
        return $filmVideos->getData($filmId, $page, $limit);
    }

    /**
     * Get short movies info
     *
     * @param array $filmIds
     *
     * @return array
     * @throws ApiException
     */
    public function getFilmsInfoShort(array $filmIds)
    {
        if (count($filmIds) > 99) {
            throw new ApiException('Max movies to fetch in one request is 99. The best is 20.');
        }

        $filmsInfoShort = new FilmsInfoShort();
        return $filmsInfoShort->getData($filmIds);
    }

    /**
     * Get user's movie votes
     *
     * @param int $userId
     * @param int $page
     * @param int $limit
     *
     * @return array
     * @throws ApiException
     */
    public function getUserFilmVotes($userId = null, $page = 0, $limit = 100)
    {
        $userId = !$userId ? $this->user['userId'] : $userId;

        if (empty($userId)) {
            throw new ApiException('You have to provide an user ID');
        }

        $userFilmVotes = new UserFilmVotes();
        return $userFilmVotes->getData($userId, $page, $limit);
    }

    /**
     * Get user's "want to see" list
     *
     * @param int $userId
     * @param int $page
     * @param int $limit
     *
     * @return array
     * @throws ApiException
     */
    public function getUserFilmsWantToSee($userId = null, $page = 0, $limit = 20)
    {
        $userId = !$userId ? $this->user['userId'] : $userId;

        if (empty($userId)) {
            throw new ApiException('You have to provide an user ID');
        }

        $userFilmsWantToSee = new UserFilmsWantToSee();
        return $userFilmsWantToSee->getData($userId, $page, $limit);
    }
    
    /**
     * Helper methods to fetch some useful data
     *
     * @return Helper
     */
    public function helper()
    {
        return new Helper($this);
    }
}
