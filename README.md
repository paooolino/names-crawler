# names-crawler
A PHP scripts for crawling the web and get a list of names and surnames divided by country

## Quick start
Install dependencies by running

    composer install
    
run tests:

    vendor/bin/phpspec run
    
start docker:

    docker-compose up -d
    
run:

    php public/fetch_names.php <type> <language>
    
    docker-compose run slim php public/fetch_names.php

    
where type is either GIVEN or FAMILY
and language is e.g. ENGLISH, ITALIAN, FRENCH, GERMAN, ...
    
run phpstan:

     vendor/bin/phpstan analyse src --level 5