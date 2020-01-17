<?php
require __DIR__ . "/../vendor/autoload.php";

/*if (count($argv) < 3) 
  die("missing arguments");

$type = $argv[1];
$language = $argv[2];
*/
$endpointUrl = 'https://query.wikidata.org/sparql';
$sparqlQueryString = <<< 'SPARQL'
SELECT ?item ?itemLabel
WHERE
{
  ?item wdt:P31 wd:Q101352.
  ?item wdt:P407 wd:Q14549.
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }
}
SPARQL;

$queryDispatcher = new App\SPARQLQueryDispatcher($endpointUrl);
$queryResult = $queryDispatcher->query($sparqlQueryString);

var_export($queryResult);
