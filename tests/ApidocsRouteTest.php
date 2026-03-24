<?php

namespace Pneves001\Apidocs\Tests;

use Pneves001\Apidocs\Facades\Apidocs;
use Pneves001\Apidocs\Facades\Param;
use Pneves001\Apidocs\Endpoints\Endpoint;
use Pneves001\Apidocs\Tests\Data\TestEndpoint;
use Pneves001\Apidocs\Exceptions\InvalidEndpoint;
use Pneves001\Apidocs\Exceptions\GroupNotFound;
use Route;

class ApidocsRouteTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        require __DIR__.'/routes/test.php';
    }

    /** @test */
    public function apidocs_compile()
    {
        apidocs([
            'test1' => TestEndpoint::class,
        ]);

        Apidocs::defer([
            'test2' => TestEndpoint::class,
            'test3' => TestEndpoint::class,
        ]);

        $this->assertCount(3, Apidocs::getDefered());
        $this->assertCount(2, Apidocs::getRoutes());

        Route::getRoutes()->refreshNameLookups();
        Apidocs::export();

        $this->assertCount(0, Apidocs::getDefered());
        $this->assertCount(5, Apidocs::getRoutes());



    }
}
