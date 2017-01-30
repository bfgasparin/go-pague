<?php

namespace GoPague;

class Payment extends Resource
{
    public static function create(array $data)
    {
        return static::request('post', 'payment_collections', $data);
    }

    public static function approve(string $uuid, string $document)
    {
        return static::request(
            'post',
            "payment_collections/{$uuid}/approve",
            ['cpf_cnpj' => $document]
        );
    }

}
