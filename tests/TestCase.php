<?php

namespace ReedJones\ApiBuilder\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Do Setup
    }

    public function tearDown(): void
    {
        // do tear down

        parent::tearDown();
    }
}
