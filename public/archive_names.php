<?php
require __DIR__ . '/../vendor/autoload.php';

$crawler = new App\Crawler();

$file = __DIR__ . "/../" . App\Crawler::NAMES_DIR . "/results.csv";
$results = array_map('str_getcsv', file($file));

$given_names = [];
$family_names = [];
foreach ($results as $r) {
  $label = $r[0];
  $gender = $r[1];
  $country = $r[2];
  $given = $r[3] == "" ? [] : explode("|", $r[3]);
  $family = $r[4] == "" ? [] : explode("|", $r[4]);
  
  $label_parts = explode(" ", $r[0]);
  if (count($label_parts) == 2) {
    if (count($given) == 0) {
      $given = [$label_parts[0]];
    }
    if (count($given) == 0) {
      $family = [$label_parts[1]];
    }
  }

  if (empty($country))
    continue;
  
  if (!empty($gender)) {
    foreach ($given as $gn) {
      if ($gn == "")
        continue;
      
      if (!isset($given_names[$country]))
        $given_names[$country] = [];

      if (!isset($given_names[$country][$gender]))
        $given_names[$country][$gender] = [];
        
      if (!isset($given_names[$country][$gender][$gn]))
        $given_names[$country][$gender][$gn] = 0;

      $given_names[$country][$gender][$gn]++;
    }
  }
  
  foreach ($family as $fn) {
    if ($fn == "")
      continue;
      
    if (!isset($family_names[$country]))
      $family_names[$country] = [];

    if (!isset($given_names[$country][$fn]))
      $family_names[$country][$fn] = 0;

    $family_names[$country][$fn]++;
  }
}

$lines = [];
foreach ($given_names as $country => $genders) {
  foreach ($genders as $gender => $names) {
    foreach ($names as $name => $f ) {
      $lines[] = [$country, $gender, $name, $f];
    }
  }
}
$crawler->save_given_names($lines);

$lines = [];
foreach ($family_names as $country => $names) {
  foreach ($names as $name => $f ) {
    $lines[] = [$country, $name, $f];
  }
}
$crawler->save_family_names($lines);


