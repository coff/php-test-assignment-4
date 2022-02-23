<?php

namespace Coff\TestAssignment\Time;

class TimeOffset
{
    protected float $seconds;

    public function __construct(string $offset) {

        // we assume ISO8601 if starts with 'P'
        if ($offset[0] === 'P') {
            $this->seconds = $this->isoToSecs($offset);
        } else {
            $this->seconds = $offset;
        }
    }

    public function toIso8601() : string {
        return $this->secsToIso($this->seconds);
    }

    public function toSeconds() : float {
        return $this->seconds;
    }

    public function __invoke() {
        return $this->toSeconds();
    }

    protected function isoToSecs($duration) {
        preg_match('/PT((\d{1,3})H)?((\d{1,2})M)?((\d{1,2}\.?\d{0,3})S)?/', $duration, $matches);

        $hours = (int) ($matches[2] ?? 0);
        $minutes = (int) ($matches[4] ?? 0);
        $seconds = (float) ($matches[6] ?? 0);

        return ($hours * 60 * 60) + ($minutes * 60) + $seconds;
    }

    protected function secsToIso(float $seconds)
    {
        if ($seconds == 0) {
            return 'PT0S';
        }

        $hours = floor($seconds / 3600);
        $seconds = fmod($seconds, 3600);

        $minutes = floor($seconds / 60);
        $seconds = fmod($seconds , 60);

        $uSeconds = round(fmod($seconds, 1),3) * 1000;

        return "PT" .
            ($hours > 0 ? sprintf("%dH", $hours) : "") .
            ($minutes > 0 ? sprintf("%dM", $minutes) : "") .
            (($seconds > 0 || $uSeconds > 0) ? sprintf('%d.%03dS', $seconds, $uSeconds) : "");
    }

    public function sub($timeOffsetOrSeconds) : self {
        if ($timeOffsetOrSeconds instanceof TimeOffset) {
            $this->seconds -= $timeOffsetOrSeconds->toSeconds();
        }

        return $this;
    }

    public function diff(TimeOffset $offset) : TimeOffset {
        return new self($this->seconds - $offset->toSeconds());
    }

}