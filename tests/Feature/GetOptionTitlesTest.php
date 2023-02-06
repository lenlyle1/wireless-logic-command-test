<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\SiteScraper;

class ScrapeOptionFieldsTest extends TestCase
{
    protected function setUp(): void
    {
        $this->dom = new \DomDocument();
        @$this->dom->loadHTMLFile('dummy-scrape.html', LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $this->XPath = new \DOMXPath($this->dom);
    }

    /**
     * Test Get Option Titles
     *
     * @return void
     */
    public function test_scraping_option_titles()
    {
        $siteScraper = new SiteScraper();
        $optionTitles = $siteScraper->getOptionTitles($this->dom, $this->XPath);

        $this->assertIsArray($optionTitles);

        $this->assertEquals(6, count($optionTitles));

        $expectedResult = [
            "Basic: 500MB Data - 12 Months",
            "Standard: 1GB Data - 12 Months",
            "Optimum: 2 GB Data - 12 Months",
            "Basic: 6GB Data - 1 Year",
            "Standard: 12GB Data - 1 Year",
            "Optimum: 24GB Data - 1 Year"
        ];

        $this->assertEquals($expectedResult, $optionTitles);

    }




}
