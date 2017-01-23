<?php

namespace GoPague;

class Store extends Model
{
    public static function all()
    {
        return static::request('get', 'stores');
    }
}
