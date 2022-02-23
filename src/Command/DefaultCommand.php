<?php

namespace Coff\TestAssignment\Command;

use Coff\TestAssignment\Processor\Processor;
use Coff\TestAssignment\Serializer\Segments;
use Coff\TestAssignment\Serializer\Silence;
use Coff\TestAssignment\Serializer\Silences;
use Coff\TestAssignment\Time\TimeOffset;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Visitor\Factory\JsonSerializationVisitorFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('generate')
            ->addOption("long", "l", InputOption::VALUE_OPTIONAL, "Sets long silence minimum duration for dividing chapters [seconds]", 2.7)
            ->addOption("short", "s", InputOption::VALUE_OPTIONAL, "Sets short silence minimum duration for dividing chapter parts [seconds]", 2.7)
            ->addOption( "max", "m", InputOption::VALUE_OPTIONAL, "Maximum chapter duration [minutes]", 30 )
            ->addArgument('silence_file',InputArgument::REQUIRED, "Path to the XML file containing discovered silence moments and their durations")

            ->setDescription('Generates the segment descriptor data out of previously discovered silence moments');
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!file_exists($input->getArgument('silence_file'))) {
            throw new InvalidArgumentException('Can\'t open specified silence_file ' . $input->getArgument('silence_file'));
        }
        $data = file_get_contents($input->getArgument('silence_file'));

        $builder = SerializerBuilder::create();
        //$builder->setDebug(true);
        $serializer = $builder->build();

        /** @var Silences $object */
        $xmlObject = $serializer->deserialize($data, Silences::class, 'xml');

        $processor = new Processor();
        $processor
            ->setMinLongSilence($input->getOption('long'))
            ->setMinShortSilence($input->getOption('short'))
            ->setMaxChapterDuration($input->getOption('max') * 60);
        /**
         * @var Silence $silence
         */
        foreach ($xmlObject->getSilences() as $silence) {

            $from = new TimeOffset($silence->getFrom());
            $until = new TimeOffset($silence->getUntil());

            $length = $until->diff($from);

            $processor->addSilence($from->toSeconds(), $length->toSeconds());

        }

        $segments = new Segments();
        $segments->setSegments( $processor->getChapters() );

        $builder = SerializerBuilder::create();
        $builder->setSerializationVisitor("json", $factory = new JsonSerializationVisitorFactory());
        $factory->setOptions(JSON_PRETTY_PRINT);
        $serializer = $builder->build();
        echo $serializer->serialize( $segments, "json");

        return 0;
    }
}