#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;

require __DIR__ . '/vendor/autoload.php';

$app = new Application('Descriptor generator');
$app->add(new \Coff\TestAssignment\Command\DefaultCommand());
$app->run();



exit;





$data = file_get_contents('examples/silence1.xml');

$builder = JMS\Serializer\SerializerBuilder::create();
$builder->setDebug(true);
//$builder->setPropertyNamingStrategy(new \JMS\Serializer\Naming\IdenticalPropertyNamingStrategy());
$serializer = $builder->build();

/** @var \Coff\TestAssignment\Serializer\Silences $object */
$xmlObject = $serializer->deserialize($data, \Coff\TestAssignment\Serializer\Silences::class, 'xml');

/*
$silences = new \Coff\TestAssignment\Serializer\Silences();
$silences->setSilences([
   new \Coff\TestAssignment\Serializer\Silence('x','y')
]);

echo $serializer->serialize($silences, "xml");

$offset1 = new \Coff\TestAssignment\Time\TimeOffset("PT10H38M14S");
$offset2 = new \Coff\TestAssignment\Time\TimeOffset("PT10H38M7.533S");

$offsetX = $offset1->diff($offset2);*/

$processor = new \Coff\TestAssignment\Processor\Processor();
$processor
    ->setMinLongSilence(3)
    ->setMinShortSilence(1.5)
    ->setMaxChapterDuration(60 * 60);
$i=0;
/**
 * @var \Coff\TestAssignment\Serializer\Silence $silence
 */
foreach ($xmlObject->getSilences() as $silence) {

    $from = new \Coff\TestAssignment\Time\TimeOffset($silence->getFrom());
    $until = new \Coff\TestAssignment\Time\TimeOffset($silence->getUntil());

    $length = $until->diff($from);
   // echo ++$i .' ' . $from->toIso8601() . ' (' . $length->toSeconds() . ')' . PHP_EOL;

    $processor->addSilence($from->toSeconds(), $length->toSeconds());

}

print_r($processor->getChapters());
