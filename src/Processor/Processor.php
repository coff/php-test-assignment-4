<?php

namespace Coff\TestAssignment\Processor;

use Coff\TestAssignment\Serializer\Segment;
use Coff\TestAssignment\Time\TimeOffset;

class Processor
{
    /** @var Segment[] */
    protected $segments = [];

    protected int $chapterNo = 1;
    protected int $partNo = 1;
    protected float $offset = 0;
    protected float $minLongSilence = 2;
    protected float $minShortSilence = 1;
    protected float $maxChapterDuration = 60 * 60;

    public function addSilence($offsetSecs, $length)
    {
        echo (new TimeOffset($offsetSecs))->toIso8601() . ' l:' . $length . PHP_EOL;

        if ($length < $this->minShortSilence) {
            echo 'Skipping due under min short silence ' . $length . PHP_EOL;
            return;
        }

        // skips segment splitting if:
        // - not a long silence moment
        // - not exceeds max chapter duration
        if ($length < $this->minLongSilence && $offsetSecs - $this->offset < $this->maxChapterDuration) {
            echo 'Skipping due not exceeding max chapter length ' . (new TimeOffset($offsetSecs - $this->offset))->toIso8601() . PHP_EOL;
            return;
        }

        echo "Adding segment..." . PHP_EOL;
        $title = 'Chapter ' . $this->chapterNo;

        // add partNo if either of the following are true:
        // - short silence moment appeared
        // - if we started counting parts before
        if (($length >= $this->minShortSilence && $length < $this->minLongSilence) || $this->partNo > 1) {
            $title .= ', part ' . $this->partNo;
            $this->partNo++;
        }

        // increment chapterNo and reset partNo when long silence occurred
        if ($length >= $this->minLongSilence) {
            $this->chapterNo++; $this->partNo = 1;
        }

        // use last offset for this segment
        $this->segments[] = new Segment($title, (new TimeOffset($this->offset))->toIso8601() );
        $this->offset = $offsetSecs;
    }

    public function run()
    {

    }

    /**
     * @param float|int $minLongSilence
     * @return Processor
     */
    public function setMinLongSilence(float|int $minLongSilence): Processor
    {
        $this->minLongSilence = $minLongSilence;
        return $this;
    }

    /**
     * @param float|int $minShortSilence
     * @return Processor
     */
    public function setMinShortSilence(float|int $minShortSilence): Processor
    {
        $this->minShortSilence = $minShortSilence;
        return $this;
    }

    /**
     * @param float|int $maxChapterDuration
     * @return Processor
     */
    public function setMaxChapterDuration(float|int $maxChapterDuration): Processor
    {
        $this->maxChapterDuration = $maxChapterDuration;
        return $this;
    }

    public function getChapters()
    {
        return $this->segments;
    }
}