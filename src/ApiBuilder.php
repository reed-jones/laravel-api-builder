<?php

namespace ReedJones\ApiBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReedJones\ApiBuilder\HasInMemoryDatabase;
use Zttp\Zttp;

class ApiBuilder extends Model
{
    use HasInMemoryDatabase;

    protected $additionalHeaders = [];

    protected function get(...$args)
    {
        return $this->send(__FUNCTION__, ...$args);
    }

    public function post(...$args)
    {
        return $this->send(__FUNCTION__, ...$args);
    }

    public function patch(...$args)
    {
        return $this->send(__FUNCTION__, ...$args);
    }

    public function put(...$args)
    {
        return $this->send(__FUNCTION__, ...$args);
    }

    public function delete(...$args)
    {
        return $this->send(__FUNCTION__, ...$args);
    }

    protected function send($method, $url, $params = [])
    {
        $fullUrl = $this->normalizeUrl($url);

        $this->data = Zttp::withHeaders($this->getHeaders())
            ->{$method}($fullUrl, $params)
            ->json();

        return $this;
    }

    protected function withPrimaryKey($key)
    {
        $this->primaryKey = $key;

        return $this;
    }

    public function withToken($token)
    {
        $this->additionalHeaders['Authorization'] = "Bearer $token";
    }

    protected function tap($callable)
    {
        $callable($this->data);

        return $this;
    }

    protected function prepare($callback = null)
    {
        if ($callback) {
            $this->data = $callback($this->data);
        }

        $this->migrate($this->data);

        return $this;
    }

    public function getHeaders()
    {
        if (method_exists($this, 'headers')) {
            return array_merge($this->headers(), $this->additionalHeaders);
        }

        if (property_exists($this, 'headers')) {
            return array_merge($this->headers, $this->additionalHeaders);
        }

        return $this->additionalHeaders;
    }

    public function getBaseUrl()
    {
        if (method_exists($this, 'baseUrl')) {
            return $this->baseUrl();
        }

        if (property_exists($this, 'baseUrl')) {
            return $this->baseUrl;
        }

        return config('app.url');
    }

    public function normalizeUrl($url)
    {
        // XOR
        if (Str::endsWith($this->getBaseUrl(), '/') ^ Str::startsWith($url, '/')) {
            return $this->getBaseUrl().$url;
        }

        // AND
        if (Str::endsWith($this->getBaseUrl(), '/')) {
            return $this->getBaseUrl().Str::replaceFirst('/', '', $url);
        }

        // NAND
        return "{$this->getBaseUrl()}/$url";
    }
}
