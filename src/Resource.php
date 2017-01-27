<?php

namespace GoPague;

use GoPague\InvalidArgumentException;
use GuzzleHttp\Client as HttpClient;

abstract class Resource
{
    use InteractsWithAPI;

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

    protected static function instanceFrom(array $data) : self
    {
        return new static($data);
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
