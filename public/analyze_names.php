<?php
// each bio involves 2 http requests
define("MAX_HTTP_REQUESTS", 60);

require __DIR__ . '/../vendor/autoload.php';

$crawler = new App\Crawler();

$names = $crawler->get_names();

$n_http_requests = 0;
$results = [];
foreach ($names as $n) {
  $bio_url = "https://it.wikipedia.org" . $n[1];
  $page = $crawler->get_page($bio_url);
  if ($page["source"] == "web") {
    $n_http_requests++;
  }
  $bio_infos = $crawler->analyze_bio($page["content"]);
  
  if (empty($bio_infos["wikidata_url"])) {
    continue;
  }
  
  $wikidata_url = $bio_infos["wikidata_url"];
  $page = $crawler->get_page($wikidata_url); 
  if ($page["source"] == "web") {
    $n_http_requests++;
  }
  $infos = $crawler->analyze_wikidata_bio($page["content"]);
  
  $results[] = $infos;
  
  if ($n_http_requests >= MAX_HTTP_REQUESTS)
    break;
}

$crawler->save_results($results);

echo $crawler->report();
