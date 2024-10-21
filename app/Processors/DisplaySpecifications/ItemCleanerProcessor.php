<?php

namespace App\Processors\DisplaySpecifications;

use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class ItemCleanerProcessor implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $item->set('Display', trim( $item->get('Display'),': '));
        $item->set('ViewingAngles', trim( $item->get('ViewingAngles'), ': '));
        $item->set('Brightness', trim( $item->get('Brightness'), ': '));
        $item->set('StaticContrast', trim( $item->get('StaticContrast'), ':, '));
        $item->set('DynamicContrast', trim($item->get('DynamicContrast'), ': '));
        $item->set('RefreshRate', trim($item->get('RefreshRate'), ': '));
        $item->set('sRGB', trim($item->get('sRGB'), ': '));
        $item->set('AdobeRGB', trim( $item->get('AdobeRGB'), ': '));
        $item->set('NTSC', trim( $item->get('NTSC'), ': '));
        return $item;
    }

}
