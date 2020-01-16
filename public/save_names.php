<?php
define("MAX_HTTP_REQUESTS", 3);

require __DIR__ . '/../vendor/autoload.php';

$crawler = new App\Crawler();

$startpages = ["https://it.wikipedia.org/wiki/Categoria:Nati_nel_2000"];
$names = [];

$n_http_requests = 0;
foreach ($startpages as $url) {
  $next_url = $url;
  do {
    $page = $crawler->get_page($next_url);
    if ($page["source"] == "web") {
      $n_http_requests++;
    }
    $infos = $crawler->analyze($page["content"]);
    $names = array_merge($names, $infos["names"]);
    $next_url = "https://it.wikipedia.org" . $infos["next_page"];
  } while(!empty($infos["next_page"]) && $n_http_requests < MAX_HTTP_REQUESTS);
}

$crawler->save_names($names);

echo $crawler->report();
