<?php

namespace GoPague;

class TaxPlan extends Resource
{
    public static function all()
    {
        return static::request('get', 'tax_plans');
    }
}
