<?php

class TimeOffsetTest extends \PHPUnit\Framework\TestCase
{
    public function iso8601Provider() {
        $h = 60 * 60; $m = 60;
        return [
            ["PT10H10M", 10 * $h + 10 * $m + 0 ],
            ["PT234H10M", 234 * $h + 10 * $m + 0 ],
            ["PT0.001S", 0 * $h + 0 * $m + 0.001 ],
            ["PT0.100S", 0 * $h + 0 * $m + 0.1 ],
            ["PT1M0.100S", 0 * $h + 1 * $m + 0.1 ],
            ["PT0.100S", 0 * $h + 0 * $m + 0.1 ],
            ["PT0.100S", 0 * $h + 0 * $m + 0.1 ],
        ];
    }

    public function secsProvider() {
        $h = 60 * 60; $m = 60;
        return [
            ["PT10H10M", 10 * $h + 10 * $m + 0 ],
            ["PT234H10M", 234 * $h + 10 * $m + 0 ],
            ["PT0.001S", 0 * $h + 0 * $m + 0.001 ],
            ["PT0.100S", 0 * $h + 0 * $m + 0.1 ],
            ["PT1M0.100S", 0 * $h + 1 * $m + 0.1 ],
            ["PT0.100S", 0 * $h + 0 * $m + 0.1 ],
            ["PT0.100S", 0 * $h + 0 * $m + 0.1 ],
        ];
    }

    /** @dataProvider iso8601Provider */
    public function testIsoToSeconds($iso, $seconds) {
        $timeOffset = new \Coff\TestAssignment\Time\TimeOffset($iso);

        $this->assertEquals($seconds, $timeOffset->toSeconds());
    }

    /** @dataProvider secsProvider */
    public function testSecondsToIso($iso, $seconds) {
        $timeOffset = new \Coff\TestAssignment\Time\TimeOffset($seconds);

        $this->assertEquals($iso, $timeOffset->toIso8601());
    }
}