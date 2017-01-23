<?php

namespace GoPague;

/**
 * Represents a GoPague Client
 *
 * @see Model
 */
class Client extends Model
{
    /**
     * Creates a new Client on GoPague service
     *
     * @param array $data
     *
     * @return self  A Client representation instance
     */
    public static function create(array $data) : self
    {
        return static::request('post', 'clients', $data);
    }


    /**
     * Gets the balance of a the client
     *
     * @param array $data
     *
     * @return self  A Client representation instance
     */
    public static function getBalance(int $clientId) : Balance
    {
        return static::request('get', 'clients/'.$clientId.'/current_balance');
    }
}
