<?php

namespace App;
use \DomDocument;
use \DOMXPath;
use \DOMNodeList;

class SiteScraper
{
    public function scrapePackages($url): string
    {
        $dom = new \DomDocument;
        @$dom->loadHTMLFile($url, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $XPath = new \DOMXPath($dom);

        $rowHeaders = $this->getRowHeaders();
        $optionTitles = $this->getOptionTitles($dom, $XPath);
        $descriptions = $this->getDescriptions($dom, $XPath);
        $pricesAndDiscounts = $this->getPricesAndDiscounts($dom, $XPath);

        list($prices, $annualPrices, $discounts) = $this->processPricesAndDiscounts($pricesAndDiscounts);

        $outputArray = $this->buildOutput(
                $rowHeaders,
                $optionTitles,
                $descriptions,
                $prices,
                $annualPrices,
                $discounts
            );

        $outputArray = $this->sortOptionsByAnnualPriceDesc($outputArray);

        return $this->createJson($outputArray);
    }

    public function buildOutput(
                $rowHeaders,
                $optionTitles,
                $descriptions,
                $prices,
                $annualPrices,
                $discounts
            ): array
    {
        $outputArray = [];
        for ($i = 0; $i < 6; $i++){
            $outputArray[$i]['option_title'] = $optionTitles[$i];
            $outputArray[$i]['description'] = $descriptions[$i];
            $outputArray[$i]['price'] = $prices[$i];
            $outputArray[$i]['annual_price'] = $annualPrices[$i];
            $outputArray[$i]['discount'] = $discounts[$i];
        }

        return $outputArray;
    }

    public function getRowHeaders(): array
    {
        return ['option_title', 'description', 'price', 'annual_price', 'discount'];
    }

    public function getOptionTitles(\DomDocument $dom, \DOMXPath $XPath): array
    {
        return $this->scrapeField($dom, $XPath, 'header dark-bg');
    }

    public function getDescriptions(\DomDocument $dom, \DOMXPath $XPath): array
    {
        return $this->scrapeField($dom, $XPath, 'package-description');
    }

    public function getPricesAndDiscounts(\DomDocument $dom, \DOMXPath $XPath): array
    {
        return $this->scrapeField($dom, $XPath, 'package-price');
    }

    public function scrapeField(\DomDocument $dom, \DOMXPath $XPath, string $class): array
    {
        $XPathQuery = "//div[@class='" . $class . "']";
        $fields = $XPath->query($XPathQuery);
        $result = [];
        foreach ($fields as $k => $field) {
            $result[] = trim($field->nodeValue);
        }

        return $result;
    }

    public function processPricesAndDiscounts(array $prices): array
    {
        $pricesArray = [];
        $annualPricesArray = [];
        $discountsArray = [];

        foreach ($prices as $price) {
            $bits = explode("\n", $price);
            $prices[] = $bits[0];
            preg_match('/[0-9.]+/', $bits[0], $matches);
            $annualPrice = $matches[0];
            if (strstr($bits[0],'Month')) {
                $annualPrice = $annualPrice * 12;
            }
            $annualPricesArray[] = (float)$annualPrice;
            $pricesArray[] = (float)$matches[0];
            $discountsArray[] = (!empty($bits[1]))?trim($bits[1]):'';
        }

        return [$pricesArray, $annualPricesArray, $discountsArray];
    }

    public function sortOptionsByAnnualPriceDesc($outputArray)
    {
        array_multisort(array_column($outputArray, 'annual_price'), SORT_DESC, $outputArray);

        return $outputArray;
    }

    public function createJson(array $outputArray): string
    {
        return json_encode($outputArray);
    }
}
