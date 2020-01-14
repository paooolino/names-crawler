<?php

namespace spec\App;

use App\Crawler;
use PhpSpec\ObjectBehavior;

class CrawlerSpec extends ObjectBehavior {
  
  function it_is_initializable() {
    
    $this->shouldHaveType(Crawler::class);
  
  }
  
}
