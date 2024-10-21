<?php

namespace App\Console\Commands;

use App\Spiders\DisplaySpecificationsSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;

class Scrape extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrape';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Roach::startSpider(DisplaySpecificationsSpider::class);
    }
}
