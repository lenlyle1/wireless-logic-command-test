<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\SiteScraper;

class GetRowHeadersTest extends TestCase
{
    protected function setUp(): void
    {
        $this->dom = new \DomDocument();
        @$this->dom->loadHTMLFile('dummy-scrape.html', LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $this->XPath = new \DOMXPath($this->dom);
    }

    /**
     * Test Get Row Headers
     *
     * @return void
     */
    public function test_getting_row_headers(): void
    {
        $siteScraper = new SiteScraper();

        $rowHeaders = $siteScraper->getRowHeaders($this->dom, $this->XPath);

        $this->assertEquals(5, count($rowHeaders));
        $this->assertIsArray($rowHeaders);
        $this->assertEquals(['option_title', 'description', 'price', 'annual_price', 'discount'], $rowHeaders);
    }
}
