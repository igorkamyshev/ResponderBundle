<?php

namespace Kamyshev\ResponderBundle\Responder;

class ResultMetadata
{
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /** @var string */
    private $type;
}
