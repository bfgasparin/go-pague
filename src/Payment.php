<?php

namespace GoPague;

class Payment extends Resource
{
    public static function create(array $data)
    {
        return static::request('post', 'payment_collections', $data);
    }
}
