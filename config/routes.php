<?php
use Cake\Routing\Router;

Router::plugin(
    'CsvViews',
    ['path' => '/csv-views'],
    function ($routes) {
        $routes->fallbacks('DashedRoute');
    }
);
