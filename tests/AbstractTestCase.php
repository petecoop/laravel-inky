<?php

namespace Rsvpify\Tests\LaravelInky;

use Rsvpify\LaravelInky\InkyServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

abstract class AbstractTestCase extends AbstractPackageTestCase
{
    protected function getServiceProviderClass($app)
    {
        return InkyServiceProvider::class;
    }
}
