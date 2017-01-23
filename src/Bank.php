<?php

namespace GoPague;

class Bank extends Resource
{
    public static function all() : array
    {
        return static::requestAll('banks');
    }
}
