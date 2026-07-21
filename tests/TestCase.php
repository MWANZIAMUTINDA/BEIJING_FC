<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Disable Vite asset compilation during tests to prevent 404s on views.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }
}
