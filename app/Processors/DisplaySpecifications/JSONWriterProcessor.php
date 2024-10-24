<?php

namespace App\Processors\DisplaySpecifications;

use Illuminate\Support\Facades\Storage;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class JSONWriterProcessor implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $jsonFile = 'monitors.json';

        if (!Storage::disk('local')->exists($jsonFile)) {
            $dataArray = ['monitors' => []];
        } else {

            $existingData = Storage::disk('local')->get($jsonFile);

            $dataArray = json_decode($existingData, true);

            if (!is_array($dataArray)) {
                $dataArray = ['monitors' => []];
            }
        }

        $dataArray['monitors'][] = $item->all();

        echo $item->get('Manufacturer') . ': ' . $item->get('Model') . PHP_EOL;

        Storage::disk('local')->put($jsonFile, json_encode($dataArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $item;
    }

}
