<?php

namespace GoPague;

/**
 * Represents a GoPague POS Transaction
 *
 * @see Resource
 */
class PosTransaction extends Resource
{
    /*
     * Gets a POS transaction by its ID
     *
     * @param string $id
     *
     * @return self  A PosTransaction representation instance
     */
    public static function find(string $id)
    {
        return static::request('get', 'pos_transactions/'.$id);
    }
}
