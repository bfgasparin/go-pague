<?php

namespace GoPague\Exceptions;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GoPagueException extends Exception
{
    protected $response;
    protected $request;

    public function __construct(
        $message,
        RequestInterface $request,
        ResponseInterface $response = null
    ) {
        // Set the code of the exception if the response is set and not future.
        $code = $response && !($response instanceof PromiseInterface)
            ? $response->getStatusCode()
            : 0
        ;

        parent::__construct($message, $code);
        $this->request = $request;
        $this->response = $response;
    }

    public static function serviceRespondedWithAnError(RequestInterface $request, ResponseInterface $response) : self
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
