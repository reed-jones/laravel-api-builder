<?php

namespace ReedJones\ApiBuilder\Tests;

use ReedJones\ApiBuilder\ApiBuilder;
use ReedJones\ApiBuilder\Tests\TestCase;

class ApiBuilderTest extends TestCase
{
    public function test_url_is_normalized()
    {
        $f = new ApiWithoutTrailingSlash;
        $a = new ApiWithTrailingSlash;

        $this->assertEquals('http://localhost:1234/items', $f->normalizeUrl('items'));
        $this->assertEquals('http://localhost:1234/items', $f->normalizeUrl('/items'));
        $this->assertEquals('http://localhost:1234/items', $a->normalizeUrl('items'));
        $this->assertEquals('http://localhost:1234/items', $a->normalizeUrl('/items'));
    }

    public function test_can_get_base_url()
    {
        $f = new ApiWithoutTrailingSlash;
        $a = new ApiWithTrailingSlash;

        $this->assertEquals('http://localhost:1234', $f->getBaseUrl());
        $this->assertEquals('http://localhost:1234/', $a->getBaseUrl());
    }

    public function test_can_get_headers()
    {
        $f = new ApiWithHeadersArray;
        $a = new ApiWithHeadersFunction;
        $d = new ApiWithoutTrailingSlash;

        $this->assertEquals(['X-TEST' => 'NEATO'], $f->getHeaders());
        $this->assertEquals(['X-TEST' => 'NEATO'], $a->getHeaders());
        $this->assertEquals([], $d->getHeaders());

        $f->withToken('great');
        $a->withToken('great');
        $d->withToken('great');

        $this->assertEquals(['X-TEST' => 'NEATO', 'Authorization' => 'Bearer great'], $f->getHeaders());
        $this->assertEquals(['X-TEST' => 'NEATO', 'Authorization' => 'Bearer great'], $a->getHeaders());
        $this->assertEquals(['Authorization' => 'Bearer great'], $d->getHeaders());
    }
}

class ApiWithoutTrailingSlash extends ApiBuilder
{
    protected $baseUrl = 'http://localhost:1234';
}

class ApiWithTrailingSlash extends ApiBuilder
{
    protected function baseUrl()
    {
        return 'http://localhost:1234/';
    }
}

class ApiWithHeadersArray extends ApiBuilder
{
    protected $baseUrl = 'http://localhost:1234';
    protected $headers = ['X-TEST' => 'NEATO'];
}

class ApiWithHeadersFunction extends ApiBuilder
{
    protected $baseUrl = 'http://localhost:1234';

    protected function headers()
    {
        return ['X-TEST' => 'NEATO'];
    }
}
