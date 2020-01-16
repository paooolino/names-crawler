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
  
  function it_analyzes_bio() {
    $html = file_get_contents("spec/sample_bio.txt");
    $result = $this->analyze_bio($html);
    $result["wikidata_url"]->shouldBe("https://www.wikidata.org/wiki/Special:EntityPage/Q56641599");
  
    $html = file_get_contents("spec/sample_bio_without_wikidata_url.txt");
    $result = $this->analyze_bio($html);
    $result["wikidata_url"]->shouldBe("");
  }
  
  function it_analyzes_wikidata_bio() {
    $html = file_get_contents("spec/sample_wikidata.txt");
    $result = $this->analyze_wikidata_bio($html);
    $result["gender"]->shouldBe("male");
    $result["country"]->shouldBe("United Kingdom");
    $result["given_names"]->shouldBe(["Maximillian", "James"]);
    $result["family_names"]->shouldBe(["Aarons"]);
    $result["title"]->shouldBe("Max Aarons");
  }
  
  function it_returns_report() {
  }
}
