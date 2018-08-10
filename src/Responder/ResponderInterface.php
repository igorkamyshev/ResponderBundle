<?php

namespace Kamyshev\ResponderBundle\Responder;

use Symfony\Component\HttpFoundation\Response;

interface ResponderInterface
{
    public function supports($result, ResultMetadata $meta): bool;

    public function make($result, ResultMetadata $meta): Response;
}
