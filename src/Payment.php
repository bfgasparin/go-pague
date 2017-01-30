<?php

namespace GoPague;

class Payment extends Resource
{
    public static function create(array $data)
    {
        return static::request('post', 'payment_collections', $data);
    }

    public function approve($document)
    {
        return static::request(
            'post',
            "payment_collections/{$this->uuid}/approve",
            ['cpf_cnpj' => $document]
        );
    }
}
