# names-crawler
A PHP scripts for crawling the web and get a list of names and surnames divided by country

## Quick start
Install dependencies by running

    composer install
    
run tests:

    vendor/bin/phpspec run
    
run: (3 steps)

1) Saves a list of names from main listing pages. Outputs in names/names.csv.
    
    php public/save_names.php
    
2) Analizes the single names pages and tries to get data from the wiki metadata 
pages.
Inputs from names/names.csv.
Outputs in names/results.csv.

    php public/analyze_names.php
    
3) Subdivides male and female given names and family names by country.
Inputs from names/result.csv.
Outputs in names/given_names.csv
Outputs in names/family_names.csv

    php public/archive_names.php

    
run phpstan:

     vendor/bin/phpstan analyse src --level 5