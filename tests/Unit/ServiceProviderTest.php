<?php

namespace SocialiteProviders\Manager\Test\Unit;

use Mockery as m;
use PHPUnit_Framework_TestCase as TestCase;
use SocialiteProviders\Manager\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class ServiceProviderTest extends TestCase
{
    use ManagerTestTrait;

    /**
     * @test
     */
    public function it_fires_socialite_was_called_event_on_boot()
    {
        $socialiteWasCalledMock = m::mock(SocialiteWasCalled::class);
        self::$functions
            ->shouldReceive('app')
            ->with(SocialiteWasCalled::class)
            ->once()
            ->andReturn($socialiteWasCalledMock);

        self::$functions
            ->shouldReceive('event')
            ->with($socialiteWasCalledMock)
            ->once();

        $serviceProvider = new ServiceProvider($this->appMock());
        $serviceProvider->boot();

        $this->assertTrue(true);
    }
}

namespace SocialiteProviders\Manager;

use SocialiteProviders\Manager\Test\Unit\ServiceProviderTest;

function app($make)
{
    return ServiceProviderTest::$functions->app($make);
}

function event($event)
{
    return ServiceProviderTest::$functions->event($event);
}
