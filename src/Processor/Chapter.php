<?php

namespace Coff\TestAssignment\Processor;

use Coff\TestAssignment\Serializer\Segment;
use Coff\TestAssignment\Time\TimeOffset;

class Chapter
{
    protected float $offsetStart;
    protected float $offsetEnd;
    protected $possibleParts = [];
    protected int $number;

    public function __construct($offsetStart, $number)
    {
        $this->offsetStart = $offsetStart;
        $this->number = $number;
    }

    /**
     * @param float $offsetEnd
     * @return Chapter
     */
    public function setOffsetEnd(float $offsetEnd): Chapter
    {
        $this->offsetEnd = $offsetEnd;
        return $this;
    }

    public function addPossiblePart($silenceEndsOffset)
    {
        $this->possibleParts[] = (new TimeOffset($silenceEndsOffset))->toIso8601();
    }

    /**
     * @return float
     */
    public function getOffsetStart(): float
    {
        return $this->offsetStart;
    }

    /**
     * @return float
     */
    public function getOffsetEnd(): float
    {
        return $this->offsetEnd;
    }

    /**
     * @return array
     */
    public function getPossibleParts(): array
    {
        return $this->possibleParts;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    public function getSegments($maxSegmentLength) {
        if (count($this->possibleParts) > 0
            && isset($this->offsetEnd)
            && $this->offsetEnd - $this->offsetStart > $maxSegmentLength
        ) {
            $segments = []; $i=1;
            $segments[] = new Segment("Chapter " . $this->number . ", part " . $i++, (new TimeOffset($this->offsetStart))->toIso8601());
            foreach ($this->possibleParts as $timeOffset) {
                $segments[] = new Segment("Chapter " . $this->number . ", part " . $i++, $timeOffset );
            }
            return $segments;
        } else {
            return [
                new Segment("Chapter " . $this->number, (new TimeOffset($this->offsetStart))->toIso8601())
            ];
        }
    }
}