<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\SiteScraper;

class GetPricesAndDescriptionTest extends TestCase
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
    public function test_getting_prices_and_subscriptions(): void
    {
        $siteScraper = new SiteScraper();


        $pricesAndDiscounts = $siteScraper->getPricesAndDiscounts($this->dom, $this->XPath);
        list($prices, $annualPrices, $discounts) = $siteScraper->processPricesAndDiscounts($pricesAndDiscounts);

        $this->assertIsArray($prices);
        $this->assertIsArray($annualPrices);
        $this->assertIsArray($discounts);

        $this->assertEquals(6, count($prices));
        $this->assertEquals(6, count($annualPrices));
        $this->assertEquals(6, count($discounts));

    }
}
