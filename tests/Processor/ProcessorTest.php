<?php

use Coff\TestAssignment\Serializer\Segment;

class ProcessorTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Coff\TestAssignment\Processor\Processor  */
    protected $object;

    public function setUp(): void
    {
        $this->object = new Coff\TestAssignment\Processor\Processor();
    }

    public function testAddSilence_underMinShortSilnce()
    {
        $this->object->setMinShortSilence(2.01);
        $this->object->addSilence(10, 2);

        $this->assertEquals(false, $this->object->addSilence(10, 2));
    }

    public function testAddSilence_addsNewChapter()
    {
        $this->object->setMinShortSilence(2);
        $this->object->setMinLongSilence(3);
        $this->object->addSilence(3600, 3);

        $this->assertCount(2, $chapters = $this->object->getChapters());

        $this->assertEquals(3603, $chapters[1]->getOffsetStart());
    }

    public function testAddSilence_addsPossiblePart()
    {
        $this->object->setMinShortSilence(2);
        $this->object->setMinLongSilence(3);
        $this->object->addSilence(3600, 2);

        $this->assertCount(1, $chapters = $this->object->getChapters());
        $this->assertCount(1, $chapters[0]->getPossibleParts());
    }

    public function silencesSegmentsProvider() {
        return [
            [
                // simple segments
                [[10,3.5], [20, 2.5], [30, 2.7]],
                ["PT0S", "PT13.500S", "PT22.500S", "PT32.700S"],
                'cccc'
            ],
            [
                // two parts and two segments
                [[16,2.0], [32, 2.5], [35, 2.5]],
                ["PT0S", "PT18.000S", "PT34.500S", "PT37.500S"],
                'ppcc'
            ],
            [
                // too short silence + too long chapter but without short valid silences gives no parts anyway
                [[16,0.9], [32, 2.5], [35, 2.5]],
                ["PT0S", "PT34.500S", "PT37.500S"],
                'ccc'
            ],
            [
                // has valid short silence but segment not too big so no divide
                [[16, 1.0], [27.5, 2.5], [35, 2.5]],
                ["PT0S", "PT30.000S", "PT37.500S"],
                'ccc'
            ],
        ];
    }

    /**
     * @dataProvider silencesSegmentsProvider
     * @param $silenceMoments
     * @param $expectedSegments
     * @param $segmentTypes
     * @return void
     */
    public function testGetSegments_generatesProperSegments($silenceMoments, $expectedSegments, $segmentTypes) {
        $this->object->setMinLongSilence(2.5);
        $this->object->setMinShortSilence(1);
        $this->object->setMaxChapterDuration(30);

        foreach ($silenceMoments as $moment ) {
            $this->object->addSilence( $moment[0], $moment[1]);
        }

        $segments = $this->object->getSegments();

        /** @var Segment $segment */
        $type = [];
        foreach ($segments as $key => $segment) {
            $type[] = strpos($segment->getTitle(), 'part') === false ? 'c' : 'p';
            $this->assertEquals($expectedSegments[$key],$segment->getOffset());
        }
        $this->assertEquals($segmentTypes, implode($type));

    }
}