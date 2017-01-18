<?php

namespace GoPague\Exceptions;

use Psr\Http\Message\ResponseInterface;

class GoPagueException extends Exception
{
    public static function serviceRespondedWithAnError(ResponseInterface $response)
    {
        return new static("Go Pague responded with an error: `{$response->getBody()->getContents()}`");
    }
}
