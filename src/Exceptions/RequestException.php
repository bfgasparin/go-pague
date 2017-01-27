<?php

namespace GoPague\Exceptions;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception throw when the there is an error on request (Response code 4xx or 5xx)
 */
class RequestException extends GoPagueException
{
    protected $response;
    protected $request;

    public function __construct($message, RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($message, $response->getStatusCode());
        $this->request = $request;
        $this->response = $response;
    }

    public static function serviceRespondedWithAnError(RequestInterface $request, ResponseInterface $response = null) : self
    {
        return new static(
            "Go Pague responded with an error: `{$response->getBody()->getContents()}`",
            $request,
            $response
        );
    }

    public function response() : ResponseInterface
    {
        return $this->response;
    }

    public function request() : RequestInterface
    {
        return $this->request;
    }
}
