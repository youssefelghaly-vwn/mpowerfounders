<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Views are rendered in full by the feature tests; without this
        // every one of them would need `npm run build` to have happened
        // first just to resolve @vite().
        $this->withoutVite();
    }
}
