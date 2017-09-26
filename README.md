filmweb.pl API Bridge v.1.1.0
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
  - getFilmDescription
  - getFilmImages
  - getFilmInfoFull
  - getFilmPersons
  - getFilmReview
  - getFilmsInfoShort
  - getFilmVideos
  - isLoggedUser
  - getUserFilmsWantToSee
  - getUserFilmVotes

## Installation
Clone this repository or use `composer require bywciu/filmweb` in your project directory.
