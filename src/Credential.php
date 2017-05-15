<?php

namespace GoPague;

/**
 * GoPague Entry point
 *
 */
class Credential extends Resource
{
    public $token;
    public $userId;
    public $clientIds;

    public function __construct($attributes)
    {
        $this->token = $attributes['authentication_token'] ?? null;
        $this->userId = $attributes['user_id'] ?? null;
        $this->clientIds = $attributes['client_ids'] ?? null;
    }

    public static function create(string $email, string $password) : self
    {
        return static::request(
            'post',
            'users/login',
            [
                'user' => ['email' => $email, 'password' => $password]
            ]
        );
    }
}
