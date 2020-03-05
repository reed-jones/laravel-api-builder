<?php

namespace ReedJones\ApiBuilder;

use ReedJones\ApiBuilder\ApiBuilder;

class Cocktail extends ApiBuilder
{
    // Free api for testing:
    // https://www.thecocktaildb.com/api.php
    protected $baseUrl = 'https://www.thecocktaildb.com/api/json/v1/1/';

    // non-standard primary key ->find($key)
    protected $primaryKey = 'idDrink';

    // Common casts shared between api calls
    protected $casts = [
        'dateModified' => 'date',
    ];

    protected function drinks()
    {
        return $this->get('search.php', ['f' => 'a'])
            ->prepare(function ($data) {
                return $data['drinks']; // the data we want is in the 'drinks' key
            });
    }

    protected function category($category = 'Cocktail')
    {
        // Override default primary key for _this_ call only
        return $this->withPrimaryKey('idDrink')
            ->get('filter.php', ['c' => $category])
            ->prepare(function ($data) {
                return $data['drinks'];
            });
    }
}
