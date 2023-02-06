<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\SiteScraper;

class CreateOutputArrayTest extends TestCase
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
    public function test_create_output_array(): void
    {
        $siteScraper = new SiteScraper();

        $rowHeaders = $siteScraper->getRowHeaders();
        $optionTitles = $siteScraper->getOptionTitles($this->dom, $this->XPath);
        $descriptions = $siteScraper->getDescriptions($this->dom, $this->XPath);
        $pricesAndDiscounts = $siteScraper->getPricesAndDiscounts($this->dom, $this->XPath);

        list($prices, $annualPrices, $discounts) = $siteScraper->processPricesAndDiscounts($pricesAndDiscounts);

        $outputArray = $siteScraper->buildOutput(
                $rowHeaders,
                $optionTitles,
                $descriptions,
                $prices,
                $annualPrices,
                $discounts
            );

        $this->assertEquals(['option_title', 'description', 'price', 'annual_price', 'discount'], array_keys($outputArray[0]));

        $outputArray = $siteScraper->sortOptionsByAnnualPriceDesc($outputArray);

        $annualPrices = [];
        foreach ($outputArray as $k => $v) {
            $annualPrices[] = $v['annual_price'];
        }

        $this->assertEquals([191.88, 174.0, 119.88, 108.0, 71.88, 66.0], $annualPrices);

    }
}
