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
    protected static function request(string $method, string $uri, array $data = null)
    {
        if ($attributes = GoPagueClient::$method($uri, $data)) {
            return static::instanceFrom($attributes);
        };

        return null;
    }

    protected static abstract function instanceFrom(array $data) : self;

    protected static function requestAll(string $uri) : array
    {
        $collection = GoPagueClient::get($uri);
        $identifier = isset(static::$identifier) ? static::$identifier : $uri;

        if(!empty($collection)) {
            return array_map(
                function ($object) {
                    return static::instancefrom(
                        $object
                    );
                },
                // some get requests does not have an identifier, so
                // filter by an identifier only if needed
                empty($identifier) ? $collection : $collection[$identifier]
            );
        }else{
            return [];
        }
    }
}
