<?php
use Cake\Routing\Router;

// Admin routes
Router::plugin(
    'Mailman',
    ['path' => '/mailman'],
    function ($routes) {

        $routes->prefix('admin', function ($routes) {

            $routes->connect('/:controller');
            $routes->fallbacks('DashedRoute');
        });
    }
);
