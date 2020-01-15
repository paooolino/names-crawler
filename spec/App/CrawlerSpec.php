<?php

namespace spec\App;

use App\Crawler;
use PhpSpec\ObjectBehavior;

class CrawlerSpec extends ObjectBehavior {
  
  function it_is_initializable() {
    
    $this->shouldHaveType(Crawler::class);
  
  }
  
  function it_analyzes_page() {
    $html = file_get_contents("spec/sample.txt");
    $result = $this->analyze($html);
    $result["names"]->shouldNotContain(["Nati nel 2000", "/wiki/Nati_nel_2000"]);
    $result["names"]->shouldContain(["Max Aarons", "/wiki/Max_Aarons"]);
    $result["names"]->shouldContain(["Sofiane Diop", "/wiki/Sofiane_Diop"]);
    $result["next_page"]->shouldBe("/w/index.php?title=Categoria:Nati_nel_2000&pagefrom=Dixion+%2CMatthew%0AMatthew+Dixon#mw-pages");
  }
  
  function it_returns_report() {
  }
}
