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

    public static function serviceRespondedWithAnError(RequestInterface $request, ResponseInterface $response = null) : self
    {
        return new static(
            "Go Pague responded with an error: `{$response->getBody()->getContents()}`",
            $request,
            $response
        );
    }

    public static function couldNotConnectToService(RequestInterface $request, Exception $e) : self
    {
        return new static(
            'Error conecting to Go Pague Server: ' . $e->getMessage(),
            $request
        );
    }

    public function response()
    {
        return $this->response;
    }

    public function request() : RequestInterface
    {
        return $this->request;
    }
}
