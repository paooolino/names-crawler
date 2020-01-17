<?php
namespace App;

class SPARQLQueryDispatcher {
  private $endpointUrl;

  public function __construct(string $endpointUrl)
  {
      $this->endpointUrl = $endpointUrl;
  }

  public function query(string $sparqlQuery): array
  {
      $url = $this->endpointUrl . '?query=' . urlencode($sparqlQuery);
  
      $response = $this->curl_get_contents($url);
      return json_decode($response, true);
  }
  
  private function curl_get_contents($url) {
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/sparql-results+json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true );
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt($ch, CURLOPT_AUTOREFERER, true );
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $raw=curl_exec($ch);
    curl_close ($ch);
    return $raw;
  }
}