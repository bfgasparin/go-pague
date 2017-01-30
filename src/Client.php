<?php

namespace GoPague;

/**
 * Represents a GoPague Client
 *
 * @see Resource
 */
class Client extends Resource
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

    public static function search(string $param)
    {
        return static::request('get', 'clients/search?q='.$param);
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
