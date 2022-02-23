<?php

namespace Coff\TestAssignment\Serializer;

use JMS\Serializer\Annotation\Type;

class Segments
{
    /**
     * @var Segment[]
     * @Type("array<Coff\TestAssignment\Serializer\Segment>")
     */
    protected $segments = [];

    /**
     * @param Segment[] $segments
     * @return Segments
     */
    public function setSegments(array $segments): Segments
    {
        $this->segments = $segments;
        return $this;
    }


}