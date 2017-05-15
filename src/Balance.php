<?php

namespace GoPague;

/**
 * Represents a GoPague Balance
 *
 * @see Resource
 */
class Balance extends Resource
{
    /*
     * Gets a Client Balance by the client id
     *
     * @param int $clientId
     *
     * @return self  A PosTransaction representation instance
     */
    public static function find(int $clientId) : self
    {
        return static::request('get', 'clients/'.$clientId.'/current_balance');
    }
}
