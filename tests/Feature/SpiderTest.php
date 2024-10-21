<?php

use App\Spiders\DisplaySpecificationsSpider;
use RoachPHP\Roach;

test('spider is called', function () {

    $runner = Roach::fake();

    $this->artisan('app:scrape');

    $runner->assertRunWasStarted(DisplaySpecificationsSpider::class);
});
