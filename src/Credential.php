<?php

namespace GoPague;

/**
 * GoPague Entry point
 *
 */
class Credential
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
}
