<?php

namespace GoPague;

class Bank extends Resource
{
    public static function all()
    {
        return static::request('get', 'banks');
    }
}
