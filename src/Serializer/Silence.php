<?php

namespace Coff\TestAssignment\Serializer;

use JMS\Serializer\Annotation\XmlAttribute;

class Silence
{
    /** @XmlAttribute */
    private string $from = "";

    /** @XmlAttribute */
    private string $until = "";

    public function __construct($from, $until)
    {
        $this->from = $from;
        $this->until = $until;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getUntil(): string
    {
        return $this->until;
    }


}