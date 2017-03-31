<?php

namespace GoPague;

class Store extends Resource
{
    public static function all()
    {
        return static::requestAll('stores');
    }
}
