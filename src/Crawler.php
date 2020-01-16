<?php
namespace App;

use voku\helper\HtmlDomParser;

final class Crawler {  
  
  const CACHE_DIR = "cache";
  const NAMES_DIR = "names";
  
  private $report_lines;
  
  public function __construct() {
    $this->createDirIfNotExists(__DIR__ . "/../" . self::CACHE_DIR);
    $this->createDirIfNotExists(__DIR__ . "/../" . self::NAMES_DIR);
  }
  
  public function get_page($url) {
    $content = "";
    
    $file = __DIR__ . "/../" . self::CACHE_DIR . "/f_" . md5($url);
    if (file_exists($file)) {
      $content = file_get_contents($file);
      $source = "cache";
      $this->report_lines[] = "Recuperata pagina da cache: " . $url;
    } else {
      $content = file_get_contents($url);
      file_put_contents($file, $content);
      $source = "web";
      $this->report_lines[] = "Recuperata pagina da web: " . $url;
    }
    
    return [
      "content" => $content,
      "source" => $source
    ];
  }
  
  public function analyze($html_string) {
    $names = [];
    $next_page = "";
    
    $dom = HtmlDomParser::str_get_html($html_string);

    $groups = $dom->findMulti('.mw-category-group');
    $names = [];
    foreach ($groups as $g) {
      $h3 = $g->findOne('h3');
      if (preg_replace('/[\W]/', '', $h3->plaintext) != "") {
        foreach ($g->findMulti("a") as $name_item) {
          $names[] = [$name_item->plaintext, $name_item->href];
        }
      }
    }
    foreach ($dom->findMulti('#mw-pages > a') as $link_item) {
      if ($link_item->plaintext == "pagina successiva") {
        $next_page = $link_item->href;
      }
    }
    
    $this->report_lines[] = "Trovati " . count($names) . " nomi";
    
    return [
      "names" => $names,
      "next_page" => htmlspecialchars_decode($next_page)
    ];
  }
  
  public function analyze_bio($html_string) {
    $wikidata_url = "";
    
    $dom = HtmlDomParser::str_get_html($html_string);
    $link = $dom->findOne('li#t-wikibase > a');
    if ($link) 
      $wikidata_url = $link->href;
    
    if ($wikidata_url == "")
      $this->report_lines[] = "WIKIDATA BIO NON TROVATA";
    
    return [
      "wikidata_url" => $wikidata_url
    ];
  }
  
  public function analyze_wikidata_bio($html_string) {
    $gender = "";
    $country = "";
    $given_names = [];
    $family_names = [];
    
    $dom = HtmlDomParser::str_get_html($html_string);
    
    $gender = $dom->findOne('#P21 .wikibase-snakview-value a')->plaintext;
    $country = $dom->findOne('#P27 .wikibase-snakview-value a')->plaintext;
    $given_names = $dom->findMulti('#P735 .wikibase-statementview-mainsnak-container .wikibase-snakview-value a')->plaintext;
    $family_names = $dom->findMulti('#P734 .wikibase-statementview-mainsnak-container .wikibase-snakview-value a')->plaintext;
    $title = $dom->findOne('.wikibase-title-label')->plaintext;
    
    return [
      "gender" => $gender,
      "country" => $country,
      "title" => $title,
      "given_names" => $given_names,
      "family_names" => $family_names
    ];
  }
  
  public function save_names($arr) {
    $file = __DIR__ . "/../" . self::NAMES_DIR . "/names.csv";
    file_put_contents($file, $this->generateCsv($arr));
  }
  
  public function save_results($arr) {
    $file = __DIR__ . "/../" . self::NAMES_DIR . "/results.csv";
    $arr = array_map(function($item) {
      return [
        $item["title"],
        $item["gender"],
        $item["country"],
        implode("|", $item["given_names"]),
        implode("|", $item["family_names"])
      ];
    }, $arr);
    file_put_contents($file, $this->generateCsv($arr));
  }
  
  public function save_given_names($arr) {
    $file = __DIR__ . "/../" . self::NAMES_DIR . "/given_names.csv";
    file_put_contents($file, $this->generateCsv($arr));
  }
  
  public function save_family_names($arr) {
    $file = __DIR__ . "/../" . self::NAMES_DIR . "/family_names.csv";
    file_put_contents($file, $this->generateCsv($arr));
  }
  
  public function get_names() {
    $file = __DIR__ . "/../" . self::NAMES_DIR . "/names.csv";
    return array_map('str_getcsv', file($file));
  }
  
  public function report() {
    return implode("\r\n", $this->report_lines);
  }
  
  private function generateCsv($data, $delimiter = ',', $enclosure = '"') {
    $handle = fopen('php://temp', 'r+');
    foreach ($data as $line) {
      fputcsv($handle, $line, $delimiter, $enclosure);
    }
    rewind($handle);
    $contents = "";
    while (!feof($handle)) {
      $contents .= fread($handle, 8192);
    }
    fclose($handle);
    return $contents;
  }
  
  private function createDirIfNotExists($path) {
    $dir = $path;
    if (!is_dir($dir))
      mkdir($dir, 0777, true);
  }
}
