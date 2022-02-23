<?php

namespace Coff\TestAssignment\Serializer;

use JMS\Serializer\Annotation\Type;

class Segment
{
    /** @Type("string") */
    protected string $title;

    /** @Type("string") */
    protected string $offset;

    public function __construct($title, $offset)
    {
        $this
            ->setTitle($title)
            ->setOffset($offset);
    }

    /**
     * @param string $title
     * @return Segment
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $offset
     * @return Segment
     */
    public function setOffset(string $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getOffset(): string
    {
        return $this->offset;
    }
}