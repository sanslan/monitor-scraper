<?php

namespace App\Processors\DisplaySpecifications;

use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class JSONWriterProcessor implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $jsonFile = 'monitors.json';

        if (!file_exists($jsonFile)) {
            $dataArray = ['monitors' => []];
        }else{

            $existingData = file_get_contents($jsonFile);

            $dataArray = json_decode($existingData, true);

            if (!is_array($dataArray)) {
                $dataArray = ['monitors' => []];
            }
        }

        $dataArray['monitors'][] = $item->all();

        echo $item->get('Manufacturer'). PHP_EOL;

        file_put_contents($jsonFile, json_encode($dataArray,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $item;
    }

}
