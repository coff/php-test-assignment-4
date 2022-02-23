<?php

namespace Coff\TestAssignment\Serializer;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @XmlRoot("silences")
 */
class Silences
{
    /**
     * @var Silence[]
     * @Type("array<Coff\TestAssignment\Serializer\Silence>")
     * @XmlList(inline = true, entry = "silence")
     */
    protected $silences = [];

    /**
     * @param Silence[] $silences
     * @return Silences
     */
    public function setSilences(array $silences): Silences
    {
        $this->silences = $silences;
        return $this;
    }


    /**
     * @return Silence[]
     */
    public function getSilences(): array
    {
        return $this->silences;
    }
}