<?php

namespace ReedJones\ApiBuilder;

use ReedJones\ApiBuilder\ApiBuilder;

class Github extends ApiBuilder
{
    protected $baseUrl = 'https://api.github.com/';

    protected function users($since = 0)
    {
        return $this->get('users', ['since' => $since])
                ->prepare(); // not data manipulation needed
    }

    protected function user($name)
    {
        return $this->get("users/$name")
                ->prepare(function ($data) {
                    return [$data]; // the single 'user' needs to be in an array to become a proper collection
                });
    }
}
