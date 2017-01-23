<?php

namespace GoPague;

class Department extends Resource
{
    public static function all() : array
    {
        return static::requestAll('departments');
    }
}
