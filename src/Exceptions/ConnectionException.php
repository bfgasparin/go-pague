<?php

namespace GoPague\Exceptions;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception throw when the there is an error on connection to server
 */
class ConnectionException extends GoPagueException
{
    protected $request;

    public function __construct( $message, RequestInterface $request,) {
        parent::__construct($message, $code);
        $this->request = $request;
    }

    public static function couldNotConnectToService(RequestInterface $request, Exception $e) : self
    {
        return new static(
            'Error conecting to Go Pague Server: ' . $e->getMessage(),
            $request
        );
    }

    public function request() : RequestInterface
    {
        return $this->request;
    }
}
