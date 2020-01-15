<?php
namespace App;

use voku\helper\HtmlDomParser;

final class Crawler {  
  
  private $report_lines;
  
  public function __construct() {
    //
  }
  
  public function get_page($url) {
    $content = "";
    
    $file = __DIR__ . "/../cache/f_" . md5($url);
    if (file_exists($file)) {
      $content = file_get_contents($file);
      $source = "cache";
      $this->report_lines[] = "Recuperata pagina da cache: " . $url;
    } else {
      $content = file_get_contents($url);
      file_put_contents($file, $content);
      $source = "web";
      $this->report_lines[] = "Recuperata pagina da <b>web</b>: " . $url;
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
      if ($h3->plaintext != "") {
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
  
  public function save_names($path, $arr) {
    file_put_contents($path, $this->generateCsv($arr));
  }
  
  public function report() {
    return implode("<br>", $this->report_lines);
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
}
