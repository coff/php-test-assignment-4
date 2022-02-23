#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;

require __DIR__ . '/vendor/autoload.php';

$app = new Application('Descriptor generator');
$app->add(new \Coff\TestAssignment\Command\DefaultCommand());
$app->run();
