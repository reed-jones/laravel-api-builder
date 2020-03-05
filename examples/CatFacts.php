<?php

namespace ReedJones\ApiBuilder;

use ReedJones\ApiBuilder\ApiBuilder;

class CatFacts extends ApiBuilder
{
    // https://alexwohlbruck.github.io/cat-facts/docs/
    protected $baseUrl = 'https://cat-fact.herokuapp.com';

    // Primary Key
    protected $primaryKey = '_id';

    // Common casts shared between api calls
    protected $casts = [
        'upvotes' => 'integer',
    ];

    // any non-raw types will be json encoded & stored as a string
    public function getUserAttribute($value)
    {
        // 'user' is an nested array, and will be serialized when saved.
        // add mutators to unserialize it on retrieval
        return json_decode($value);
    }

    protected function facts()
    {
        return $this->get('facts')
            // ->prepare(fn($data) => $data['all']) // php 7.4+
            ->prepare(function ($data) {
                return $data['all']; // the data we want is in the 'all' key
            });
    }
}
