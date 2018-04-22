<?php

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->setPsr4('Chubbyphp\Tests\DeserializationDoctrine\\', __DIR__. '/Doctrine');
$loader->setPsr4('Chubbyphp\Tests\Deserialization\\', __DIR__);
