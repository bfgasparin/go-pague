<?php

namespace GoPague;

class Department extends Model
{
    public static function all() : array
    {
        return static::requestAll('departments');
    }
}
