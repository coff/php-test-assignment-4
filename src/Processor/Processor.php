<?php

namespace Coff\TestAssignment\Processor;

use Coff\TestAssignment\Serializer\Segment;
use Coff\TestAssignment\Time\TimeOffset;

class Processor
{
    /** @var Chapter[] */
    protected $chapters = [];

    /** @var Chapter */
    protected $lastChapter;

    protected float $minLongSilence = 2;
    protected float $minShortSilence = 1;
    protected float $maxChapterDuration = 60 * 60;

    public function __construct()
    {
        $this->chapters[] = $this->lastChapter = new Chapter(0, 1);
    }

    public function addSilence($offsetSecs, $length)
    {
        if ($length < $this->minShortSilence) {
            return false;
        }

        if ($length >= $this->minLongSilence) {
            $this->lastChapter->setOffsetEnd($offsetSecs);
            $this->lastChapter = new Chapter($offsetSecs + $length, $this->lastChapter->getNumber()+1);
            $this->chapters[] = $this->lastChapter;
        } else {
            $this->lastChapter->addPossiblePart($offsetSecs+$length);
        }
        return true;
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

    /**
     * @return Chapter[]
     */
    public function getChapters(): array
    {
        return $this->chapters;
    }

    public function getSegments()
    {
        $segments = [];
        foreach ($this->chapters as $chapter) {
            $segments = array_merge($segments, $chapter->getSegments($this->maxChapterDuration) );
        }
        return $segments;
    }
}