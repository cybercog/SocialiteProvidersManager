<?php

namespace SocialiteProviders\Manager\Test\Unit\Helpers;

use SocialiteProviders\Manager\Exception\MissingConfigException;
use SocialiteProviders\Manager\Helpers\ServicesConfigRetriever;
use SocialiteProviders\Manager\Test\Unit\ManagerTestTrait;

class ConfigRetrieverTest extends \PHPUnit_Framework_TestCase
{
    use ManagerTestTrait;

    /**
     * @test
     */
    public function it_throws_if_there_is_a_problem_with_the_services_config()
    {
        $this->expectException(MissingConfigException::class);

        $providerName = 'test';
        self::$functions
            ->shouldReceive('config')
            ->with("services.{$providerName}")
            ->once()
            ->andReturn(null);
        $configRetriever = new ServicesConfigRetriever();

        $configRetriever->getConfig($providerName)->get();
    }

    /**
     * @test
     */
    public function it_throws_if_there_are_missing_items_in_the_services_config()
    {
        $this->expectException(MissingConfigException::class);

        $providerName = 'test';
        self::$functions
            ->shouldReceive('config')
            ->with("services.{$providerName}")
            ->once()
            ->andReturn([]);
        $configRetriever = new ServicesConfigRetriever();

        $configRetriever->getConfig($providerName)->get();
    }

    /**
     * @test
     */
    public function it_retrieves_a_config_from_the_services()
    {
        $providerName = 'test';
        $clientId = 'key';
        $clientSecret = 'secret';
        $redirect = 'uri';
        $options = [
            'region' => 'eu',
            'secured' => true,
        ];
        $additionalKeys = [
            'app_id' => 'test-app-id',
            'options' => $options,
        ];
        $config = array_merge([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect' => $redirect,
        ], $additionalKeys);
        self::$functions
            ->shouldReceive('config')
            ->with("services.{$providerName}")
            ->once()
            ->andReturn($config);
        $configRetriever = new ServicesConfigRetriever();

        $configArray = $configRetriever->getConfig($providerName)->toArray();
        $this->assertCount(3, $configArray);
        $this->assertSame($clientId, $configArray['client_id']);
        $this->assertSame($clientSecret, $configArray['client_secret']);
        $this->assertSame($redirect, $configArray['redirect']);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_client_id_in_service_config_is_missing()
    {
        $this->expectException(MissingConfigException::class);

        $providerName = 'test';
        $config = [
            'client_secret' => 'secret',
            'redirect' => 'uri',
        ];
        self::$functions
            ->shouldReceive('config')
            ->with("services.{$providerName}")
            ->once()
            ->andReturn($config);
        $configRetriever = new ServicesConfigRetriever();

        $configRetriever->getConfig($providerName);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_client_secret_in_service_config_is_missing()
    {
        $this->expectException(MissingConfigException::class);

        $providerName = 'test';
        $config = [
            'client_id' => 'id',
            'redirect' => 'uri',
        ];
        self::$functions
            ->shouldReceive('config')
            ->with("services.{$providerName}")
            ->once()
            ->andReturn($config);
        $configRetriever = new ServicesConfigRetriever();

        $configRetriever->getConfig($providerName);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_redirect_in_service_config_is_missing()
    {
        $this->expectException(MissingConfigException::class);

        $providerName = 'test';
        $config = [
            'client_id' => 'id',
            'client_secret' => 'secret',
        ];
        self::$functions
            ->shouldReceive('config')
            ->with("services.{$providerName}")
            ->once()
            ->andReturn($config);
        $configRetriever = new ServicesConfigRetriever();

        $configRetriever->getConfig($providerName);
    }

    /**
     * @test
     */
    public function it_retrieves_a_config_with_additional_keys()
    {
        $providerName = 'test';
        $clientId = 'key';
        $clientSecret = 'secret';
        $redirect = 'uri';
        $options = [
            'region' => 'eu',
            'secured' => true,
        ];
        $additionalKeys = [
            'app_id' => 'test-app-id',
            'options' => $options,
        ];
        $config = array_merge([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect' => $redirect,
        ], $additionalKeys);
        self::$functions
            ->shouldReceive('config')
            ->with("services.{$providerName}")
            ->once()
            ->andReturn($config);
        $configRetriever = new ServicesConfigRetriever();

        $configArray = $configRetriever->getConfig($providerName, ['app_id', 'options', 'not-exists'])->toArray();
        $this->assertCount(6, $configArray);
        $this->assertSame($clientId, $configArray['client_id']);
        $this->assertSame($clientSecret, $configArray['client_secret']);
        $this->assertSame($redirect, $configArray['redirect']);
        $this->assertSame('test-app-id', $configArray['app_id']);
        $this->assertSame($options, $configArray['options']);
        $this->assertEmpty($configArray['not-exists']);
    }
}

namespace SocialiteProviders\Manager\Helpers;

use SocialiteProviders\Manager\Test\Unit\Helpers\ConfigRetrieverTest;

function env($key)
{
    return ConfigRetrieverTest::$functions->env($key);
}

function config($key)
{
    return ConfigRetrieverTest::$functions->config($key);
}

function app()
{
    return new applicationStub();
}

class applicationStub
{
    public function runningInConsole()
    {
        return false;
    }
}
