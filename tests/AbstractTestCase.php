<?php

namespace fibis\Tests\LaravelInky;

use fibis\LaravelInky\InkyServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

abstract class AbstractTestCase extends AbstractPackageTestCase
{
    protected function getServiceProviderClass($app)
    {
        return InkyServiceProvider::class;
    }
}