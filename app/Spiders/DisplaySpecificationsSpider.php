<?php

namespace App\Spiders;

use App\Processors\DisplaySpecifications\ItemCleanerProcessor;
use App\Processors\DisplaySpecifications\JSONWriterProcessor;
use Exception;
use Generator;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use Symfony\Component\DomCrawler\Crawler;

class DisplaySpecificationsSpider extends BasicSpider
{
    public array $downloaderMiddleware = [];

    /** @return Request[] */
    protected function initialRequests(): array
    {
        return [
            $this->sendRequest(config('roach.initial_request'), 'parse')
        ];
    }

    private function sendRequest(string $url, string $parseMethod): Request
    {
            return new Request(
                'POST',
                config('roach.proxy_url'),
                [$this, $parseMethod],
                [
                    'auth' => [config('roach.proxy_key'), ''],
                    'headers' => ['Accept-Encoding' => 'gzip'],
                    'json' => [
                        'url' => $url,
                        'httpResponseBody' => true
                    ]
                ]
            );
    }

    private function getCrawler($response): Crawler
    {
        $httpResponse = json_decode($response->getBody(), true);

        $httpResponseBody = base64_decode($httpResponse['httpResponseBody']);

        return new Crawler($httpResponseBody);
    }

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        ItemCleanerProcessor::class,
        JSONWriterProcessor::class
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 1;

    public int $requestDelay = 1;

    /**
     * @param Response $response
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $brands = [];

        $this->getCrawler($response)->filter('div.brand-listing-container-frontpage a')->each(function ($node) use (&$brands) {
            $brands[$node->text()] = $node->attr('href');
        });

        foreach ($brands as $name => $url){
            yield ParseResult::fromValue($this->sendRequest($url, 'parseBrandPages'));
        }

    }

    public function parseBrandPages(Response $response): \Generator
    {
        $models = [];

        $this->getCrawler($response)->filter('div[id^="model_"] h3 a')->each(function ($node) use (&$models) {
            $models[$node->text()] = $node->attr('href');
        });

        foreach ($models as $name => $url){
            yield ParseResult::fromValue($this->sendRequest( $url, 'parseModel'));
        }
    }

    public function parseModel(Response $response): \Generator
    {
        $mainData = $this->getCrawler($response)->filter('#main > div')->eq(3)->filter('table')->first();

        try {
            $manufacturer = $mainData->filter('tr')->reduce(function ($node) {
                return str_contains($node->filter('td')->first()->text(), 'Brand');
            })->filter('td')->eq(1)->text();
        }catch (Exception){
            $manufacturer= 'NA';
        }

        try {
            $model = $mainData->filter('tr')->reduce(function ($node) {
                return str_contains($node->filter('td')->first()->text(), 'Model');
            })->filter('td')->eq(1)->text();
        }catch (Exception){
            $model = 'NA';
        }

        try {
            $series = $mainData->filter('tr')->reduce(function ($node) {
                return str_contains($node->filter('td')->first()->text(), 'Series');
            })->filter('td')->eq(1)->text();
        }catch (Exception){
            $series= 'NA';
        }

        $displayData = $this->getCrawler($response)->filter('#main > div')->eq(3)->filter('table')->eq(1);
        try {
            $backlight = $displayData->filter('tr')
                ->reduce(fn($node) => str_contains($node->filter('td')->first()->text(), 'Backlight') )
                ->filter('td')->eq(1)->text();
        }catch (Exception){
            $backlight = 'NA';
        }

        try {
            $display = $this->getCrawler($response)->filter('#model-brief-specifications b:contains("Display")')->getNode(0)->nextSibling->nodeValue;
        }catch (Exception){
            $display = 'NA';
        }
        try {
            $viewingAngles = $this->getCrawler($response)->filter('#model-brief-specifications b:contains("Viewing angles")')->getNode(0)->nextSibling->nodeValue;
        }catch (Exception){
            $viewingAngles = 'NA';
        }
        try {
            $brightness = $this->getCrawler($response)->filter('#model-brief-specifications b:contains("Brightness")')->getNode(0)->nextSibling->nodeValue;
        }catch (Exception){
            $brightness = 'NA';
        }
        try {
            $staticContrast = $this->getCrawler($response)->filter('#model-brief-specifications b:contains("Static contrast")')->getNode(0)->nextSibling->nodeValue;
        }catch (Exception){
            $staticContrast = 'NA';
        }
        try {
            $dynamicContrast = $this->getCrawler($response)->filter('#model-brief-specifications b:contains("Dynamic contrast")')->getNode(0)->nextSibling->nodeValue;
        }catch (Exception){
            $dynamicContrast = 'NA';
        }
        try {
            $refreshRate = $this->getCrawler($response)->filter('#model-brief-specifications b:contains("Refresh rate")')->getNode(0)->nextSibling->nodeValue;
        }catch (Exception){
            $refreshRate = 'NA';
        }
        try {
            $sRGB = $this->getCrawler($response)->filter('#model-brief-specifications b:contains("sRGB")')->getNode(0)->nextSibling->nodeValue;
        }catch (Exception){
            $sRGB = 'NA';
        }
        try {
            $adobeRGB = $this->getCrawler($response)->filter('#model-brief-specifications b:contains("sRGB")')->getNode(0)->nextSibling->nodeValue;
        }catch (Exception){
            $adobeRGB = 'NA';
        }
        try {
            $nTSC = $this->getCrawler($response)->filter('#model-brief-specifications b:contains("NTSC")')->getNode(0)->nextSibling->nodeValue;
        }catch (Exception){
            $nTSC = 'NA';
        }

        $modelData = [
            "Manufacturer" => $manufacturer,
            "Series" =>  $series,
            "Model" =>  $model,
            "Backlight"=>$backlight,
            "Display"=> $display,
            "ViewingAngles"=> $viewingAngles,
            "Brightness"=> $brightness,
            "StaticContrast"=> $staticContrast,
            "DynamicContrast"=> $dynamicContrast,
            "RefreshRate"=> $refreshRate,
            "sRGB"=> $sRGB,
            "AdobeRGB"=> $adobeRGB,
            "NTSC"=> $nTSC
        ];

        yield $this->item($modelData);
    }
}
