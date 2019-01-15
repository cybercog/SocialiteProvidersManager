<?php

namespace SocialiteProviders\Manager\Test;

use Laravel\Socialite\Contracts\Factory as SocialiteFactoryContract;
use Mockery as m;
use SocialiteProviders\Manager\Config;
use SocialiteProviders\Manager\Exception\InvalidArgumentException;
use SocialiteProviders\Manager\Exception\MissingConfigException;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Manager\Test\Stubs\OAuth2ProviderStub;

class OAuth2ProviderTest extends \PHPUnit_Framework_TestCase
{
    use ManagerTestTrait;

    /**
     * @test
     */
    public function it_throws_if_there_is_no_config_in_services_or_env()
    {
        $this->expectException(MissingConfigException::class);

        $providerName = 'bar';
        $providerClass = $this->oauth2ProviderStubClass();
        $socialite = $this->socialiteMock();
        $socialite
            ->shouldReceive('buildProvider')
            ->withArgs([$providerClass, $this->config()])
            ->andReturn($this->oauth2ProviderStub());
        $socialite
            ->shouldReceive('extend')
            ->withArgs([
                $providerName,
                m::on(function ($closure) use ($providerClass) {
                    $this->assertInstanceOf($providerClass, $closure());

                    return is_callable($closure);
                }),
            ]);

        $app = $this->appMock();
        $app
            ->shouldReceive('make')
            ->with(SocialiteFactoryContract::class)
            ->andReturn($socialite);
        $configRetriever = $this->configRetrieverMock();
        $configRetriever
            ->shouldReceive('fromServices')
            ->andThrow(MissingConfigException::class);
        $event = new SocialiteWasCalled($app, $configRetriever);

        $event->extendSocialite($providerName, $providerClass);
    }

    /**
     * @test
     */
    public function it_allows_the_config_to_be_retrieved_from_the_services_array()
    {
        $providerName = 'bar';
        $providerClass = $this->oauth2ProviderStubClass();
        $socialite = $this->socialiteMock();
        $socialite
            ->shouldReceive('buildProvider')
            ->withArgs([$providerClass, $this->config()])
            ->andReturn($this->oauth2ProviderStub());
        $socialite
            ->shouldReceive('extend')
            ->withArgs([
                $providerName,
                m::on(function ($closure) use ($providerClass) {
                    $this->assertInstanceOf($providerClass, $closure());

                    return is_callable($closure);
                }),
            ]);
        $config = $this->configObject();
        $app = $this->appMock();
        $app
            ->shouldReceive('make')
            ->with(SocialiteFactoryContract::class)
            ->andReturn($socialite);
        $configRetriever = $this->configRetrieverMock();
        $configRetriever
            ->shouldReceive('fromServices')
            ->andReturn($config);
        $event = new SocialiteWasCalled($app, $configRetriever);

        $event->extendSocialite($providerName, $providerClass);
    }

    /**
     * @test
     */
    public function it_allows_a_custom_config_to_be_passed_dynamically()
    {
        $provider = new OAuth2ProviderStub(
            $this->buildRequest(),
            'client id',
            'client secret',
            'redirect url'
        );

        $result = $provider->setConfig(new Config('key', 'secret', 'callback uri'));

        $this->assertEquals($provider, $result);
    }

    /**
     * @test
     */
    public function it_retrieves_from_the_config_if_no_config_is_provided()
    {
        $providerName = 'bar';
        $providerClass = $this->oauth2ProviderStubClass();
        $socialite = $this->socialiteMock();
        $socialite
            ->shouldReceive('buildProvider')
            ->withArgs([$providerClass, $this->config()])
            ->andReturn($this->oauth2ProviderStub());
        $socialite
            ->shouldReceive('extend')
            ->withArgs([
                $providerName,
                m::on(function ($closure) use ($providerClass) {
                    $this->assertInstanceOf($providerClass, $closure());

                    return is_callable($closure);
                }),
            ]);
        $app = $this->appMock();
        $app
            ->shouldReceive('make')
            ->with(SocialiteFactoryContract::class)
            ->andReturn($socialite);
        $configRetriever = $this->configRetrieverMockWithDefaultExpectations(
            $providerName,
            $providerClass
        );
        $event = new SocialiteWasCalled($app, $configRetriever);

        $event->extendSocialite($providerName, $providerClass);
    }

    /**
     * @test
     */
    public function it_should_build_a_provider_and_extend_socialite()
    {
        $providerName = 'bar';
        $providerClass = $this->oauth2ProviderStubClass();
        $socialite = $this->socialiteMock();
        $socialite
            ->shouldReceive('buildProvider')
            ->withArgs([$providerClass, $this->config()])
            ->andReturn($this->oauth2ProviderStub());
        $socialite
            ->shouldReceive('extend')
            ->withArgs([
                $providerName,
                m::on(function ($closure) use ($providerClass) {
                    $this->assertInstanceOf($providerClass, $closure());

                    return is_callable($closure);
                }),
            ]);
        $config = $this->configObject();
        $app = $this->appMock();
        $app
            ->shouldReceive('make')
            ->with(SocialiteFactoryContract::class)
            ->andReturn($socialite);
        $app
            ->shouldReceive('make')
            ->with("SocialiteProviders.config.{$providerName}")
            ->andReturn($config);
        $configRetriever = $this->configRetrieverMockWithDefaultExpectations(
            $providerName,
            $providerClass
        );
        $event = new SocialiteWasCalled($app, $configRetriever);

        $event->extendSocialite($providerName, $providerClass);
    }

    /**
     * @test
     * @expectedException \SocialiteProviders\Manager\Exception\InvalidArgumentException
     */
    public function it_throws_if_given_a_bad_provider_class_name()
    {
        $providerName = 'bar';
        $providerClass = $this->oauth2ProviderStubClass();
        $socialite = $this->socialiteMock();
        $socialite
            ->shouldReceive('buildProvider')
            ->withArgs([$providerClass, $this->config()])
            ->andReturn($this->oauth2ProviderStub());
        $socialite
            ->shouldReceive('extend')
            ->withArgs([
                $providerName,
                m::on(function ($closure) use ($providerClass) {
                    $this->assertInstanceOf($providerClass, $closure());

                    return is_callable($closure);
                }),
            ]);
        $config = $this->configObject();
        $app = $this->appMock();
        $app
            ->shouldReceive('make')
            ->with(SocialiteFactoryContract::class)
            ->andReturn($socialite);
        $app
            ->shouldReceive('make')
            ->with("SocialiteProviders.config.{$providerName}")
            ->andReturn($config);
        $configRetriever = $this->configRetrieverMockWithDefaultExpectations(
            $providerName,
            $providerClass
        );
        $event = new SocialiteWasCalled($app, $configRetriever);

        $event->extendSocialite($providerName, $this->invalidClass());
    }

    /**
     * @test
     */
    public function it_throws_if_given_an_invalid_oauth2_provider()
    {
        $this->expectException(InvalidArgumentException::class);

        $providerName = 'foo';
        $providerClass = $this->oauth2ProviderStubClass();
        $socialite = $this->socialiteMock();
        $app = $this->appMock();
        $app
            ->shouldReceive('make')
            ->andReturn($socialite);
        $configRetriever = $this->configRetrieverMockWithDefaultExpectations(
            $providerName,
            $providerClass
        );
        $event = new SocialiteWasCalled($app, $configRetriever);

        $event->extendSocialite($providerName, $this->invalidClass());
    }

    /**
     * @test
     */
    public function it_throws_if_oauth1_server_is_passed_for_oauth2()
    {
        $this->expectException(InvalidArgumentException::class);

        $providerName = 'baz';
        $providerClass = $this->oauth2ProviderStubClass();
        $socialite = $this->socialiteMock();
        $socialite
            ->shouldReceive('formatConfig')
            ->with($this->config())
            ->andReturn($this->oauth1FormattedConfig($this->config()));

        $app = $this->appMock();
        $app
            ->shouldReceive('make')
            ->andReturn($socialite);
        $configRetriever = $this->configRetrieverMockWithDefaultExpectations(
            $providerName,
            $providerClass
        );
        $event = new SocialiteWasCalled($app, $configRetriever);

        $event->extendSocialite($providerName, $providerClass, $this->oauth1ServerStubClass());
    }
}
