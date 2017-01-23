<?php

namespace GoPague;

class TaxPlan extends Model
{
    public static function all()
    {
        return static::request('get', 'tax_plans');
    }
}
