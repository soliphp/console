<?php

$autoloader = require dirname(__DIR__) . '/vendor/autoload.php';
$autoloader->addPsr4("App\\", __DIR__ . '/app');
