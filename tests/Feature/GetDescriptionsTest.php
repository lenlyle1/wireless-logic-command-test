<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\SiteScraper;

class GetDescriptionsTest extends TestCase
{
    protected function setUp(): void
    {
        $this->dom = new \DomDocument();
        @$this->dom->loadHTMLFile('dummy-scrape.html', LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $this->XPath = new \DOMXPath($this->dom);
    }

    /**
     * Test Get Descriptions
     *
     * @return void
     */
    public function test_scraping_decriptions(): void
    {
        $siteScraper = new SiteScraper();
        $descriptions = $siteScraper->getDescriptions($this->dom, $this->XPath);

        $this->assertIsArray($descriptions);

        $this->assertEquals(6, count($descriptions));

        $this->assertEquals('Up to 500MB of data per monthincluding 20 SMS(5p / MB data and 4p / SMS thereafter)', $descriptions[0]);
    }
}
