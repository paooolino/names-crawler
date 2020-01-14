<?php

$crawler = new Crawler();

foreach ($urls as $u) {
  $content = $crawler->readUrl($u);
}