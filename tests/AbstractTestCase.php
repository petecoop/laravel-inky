<?php

namespace Petecoop\Tests\LaravelInky;

use Petecoop\LaravelInky\InkyServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

abstract class AbstractTestCase extends AbstractPackageTestCase
{
    protected function getServiceProviderClass($app)
    {
        return InkyServiceProvider::class;
    }
}