<?php

namespace GoPague;

use GoPague\InvalidArgumentException;
use GuzzleHttp\Client as HttpClient;

/**
 * Contains static methods to help Resource classes to
 * create helper methods to interact with the GoPague API
 */
trait class InteractsWithAPI
{
    protected static function request(string $method, string $uri, array $data = null) : self
    {
        return self::instanceFrom(
            GoPague::$method($uri, $data)
        );
    }

    protected static abstract function instanceFrom(array $data) : self;

    protected static function requestAll(string $uri) : array
    {
        $collection = GoPague::get($uri);
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
