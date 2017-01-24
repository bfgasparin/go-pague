<?php

namespace GoPague;

use GoPague\InvalidArgumentException;
use GuzzleHttp\Client as HttpClient;

abstract class Resource
{
    protected $attributes = [];


    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Get an attribute from the object
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (!array_key_exists($key, $this->attributes)) {
            throw new InvalidArgumentException("The {self::class} has not the {$key} attribute");
        }

        return $this->attributes[$key];
    }

    protected static function request(string $method, string $uri, array $data = null) : self
    {
        return self::instanceFrom(
            GoPague::$method($uri, $data)
        );
    }

    protected static function instanceFrom(array $data) : self
    {
        return new static($data);
    }

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

    /**
     * Returns the string representation of the resource
     *
     * @return string
     */
    public function __toString() : string
    {
        return json_encode($this->attributes);
    }

}
