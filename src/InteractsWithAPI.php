<?php

namespace GoPague;

use GoPague\Http\Client as GoPagueClient;
use GoPague\InvalidArgumentException;

/**
 * Contains static methods to help Resource classes to
 * create helper methods to interact with the GoPague API
 */
trait InteractsWithAPI
{
    protected static function request(string $method, string $uri, array $data = null) : self
    {
        return self::instanceFrom(
            GoPagueClient::$method($uri, $data)
        );
    }

    protected static abstract function instanceFrom(array $data) : self;

    protected static function requestAll(string $uri) : array
    {
        $collection = GoPagueClient::get($uri);
        $identifier = isset(static::$identifier) ? static::$identifier : $uri;

        if(!empty($collection)) {
            return array_map(
                function ($object) {
                    return self::instanceFrom(
                        $object
                    );
                },
                $collection[$identifier]
            );
        }else{
            return [];
        }
    }
}
