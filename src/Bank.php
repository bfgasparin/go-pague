<?php

namespace GoPague;

class Bank extends Model
{
    public static function all()
    {
        return static::request('get', 'banks');
    }
}
