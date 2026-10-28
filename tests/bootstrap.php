<?php
require __DIR__ . '/../vendor/autoload.php';

Tests\Support\AppServer::start();
register_shutdown_function([Tests\Support\AppServer::class, 'stop']);
