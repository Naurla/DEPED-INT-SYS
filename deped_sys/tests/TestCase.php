<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Remove the "use CreatesApplication;" line completely

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable Vite to prevent manifest errors during feature tests
        $this->withoutVite();
    }
}