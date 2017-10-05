filmweb.pl API Bridge v.1.1.5
=============================

filmweb.pl API Bridge based on Android application v.2.8

```php
$filmweb = new \Bywciu\Filmweb\Api('login', 'password');
$filmweb->getFilmDescription(123456); // get movie details with ID 123456
```

## Supported endpoints
- POST
  - login
- GET
  - getFilmDescription - get only movie description
  - getFilmImages - get all movie images
  - getFilmInfoFull - get full movie informations
  - getFilmPersons - get people related with a movie
  - getFilmReview - get movie review
  - getFilmsInfoShort - get short information about movie(s)
  - getFilmVideos - get all movie videos (trailers)
  - isLoggedUser - check if user is already logged in
  - getUserFilmsWantToSee - get user's list of wanted movies
  - getUserFilmVotes - get user's votes

```php
$filmwebHelper = $filmweb->helper();
$filmwebHelper->getUserFilmsWantToSeeInfo();
```

## Helper methods
- GET
  - getUserFilmsWantToSeeInfo - get user's list of wanted movies with short details

## Installation
Clone this repository or use `composer require bywciu/filmweb` in your project directory.
