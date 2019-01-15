<?php

namespace SocialiteProviders\Manager\Test;

use Illuminate\Contracts\Session\Session as SessionContract;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\User as SocialiteOAuth2User;
use Mockery as m;
use PHPUnit_Framework_TestCase;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OAuthTwoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function redirectGeneratesTheProperSymfonyRedirectResponse()
    {
        $session = m::mock(SessionContract::class);
        $request = Request::create('foo');
        $request->setLaravelSession($session);
        $session
            ->shouldReceive('put')
            ->once();
        $provider = new OAuthTwoTestProviderStub($request, 'client_id', 'client_secret', 'redirect');
        $response = $provider->redirect();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('http://auth.url', $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function it_can_return_the_service_container_key()
    {
        $result = OAuthTwoTestProviderStub::serviceContainerKey(OAuthTwoTestProviderStub::PROVIDER_NAME);

        $this->assertEquals('SocialiteProviders.config.test', $result);
    }

    /**
     * @test
     */
    public function userReturnsAUserInstanceForTheAuthenticatedRequest()
    {
        $session = m::mock(SessionInterface::class);
        $request = Request::create('foo', 'GET', [
            'state' => str_repeat('A', 40),
            'code' => 'code',
        ]);
        $request->setSession($session);
        $session
            ->shouldReceive('pull')
            ->once()
            ->with('state')
            ->andReturn(str_repeat('A', 40));
        $provider = new OAuthTwoTestProviderStub($request, 'client_id', 'client_secret', 'redirect_uri');
        $provider->http = m::mock('StdClass');
        $provider->http
            ->shouldReceive('post')
            ->once()
            ->with('http://token.url', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'form_params' => [
                    'client_id' => 'client_id',
                    'client_secret' => 'client_secret',
                    'code' => 'code',
                    'redirect_uri' => 'redirect_uri',
                ],
            ])
            ->andReturn($response = m::mock('StdClass'));
        $response
            ->shouldReceive('getBody')
            ->andReturn('{"access_token": "access_token", "test": "test"}');
        $user = $provider->user();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('foo', $user->id);
    }

    /**
     * @test
     */
    public function access_token_response_body_is_accessible_from_user()
    {
        $session = m::mock(SessionInterface::class);
        $accessTokenResponseBody = '{"access_token": "access_token", "test": "test"}';
        $request = Request::create('foo', 'GET', [
            'state' => str_repeat('A', 40),
            'code' => 'code',
        ]);
        $request->setSession($session);
        $session
            ->shouldReceive('pull')
            ->once()
            ->with('state')
            ->andReturn(str_repeat('A', 40));
        $provider = new OAuthTwoTestProviderStub($request, 'client_id', 'client_secret', 'redirect_uri');
        $provider->http = m::mock('StdClass');
        $provider->http
            ->shouldReceive('post')
            ->once()
            ->with('http://token.url', [
                'headers' => [
                    'Accept' => 'application/json',
                ], 'form_params' => [
                    'client_id' => 'client_id',
                    'client_secret' => 'client_secret',
                    'code' => 'code',
                    'redirect_uri' => 'redirect_uri',
                ],
            ])
            ->andReturn($response = m::mock('StdClass'));
        $response
            ->shouldReceive('getBody')
            ->andReturn($accessTokenResponseBody);
        $user = $provider->user();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('foo', $user->id);
        $this->assertEquals($user->accessTokenResponseBody, json_decode($accessTokenResponseBody, true));
    }

    /**
     * @test
     */
    public function regular_laravel_socialite_class_works_as_well()
    {
        $session = m::mock(SessionInterface::class);
        $accessTokenResponseBody = '{"access_token": "access_token", "test": "test"}';
        $request = Request::create('foo', 'GET', [
            'state' => str_repeat('A', 40),
            'code' => 'code',
        ]);
        $request->setSession($session);
        $session
            ->shouldReceive('pull')
            ->once()
            ->with('state')
            ->andReturn(str_repeat('A', 40));
        $provider = new OAuthTwoTestProviderStub($request, 'client_id', 'client_secret', 'redirect_uri');
        $provider->http = m::mock('StdClass');
        $provider->http
            ->shouldReceive('post')
            ->once()
            ->with('http://token.url', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'form_params' => [
                    'client_id' => 'client_id',
                    'client_secret' => 'client_secret',
                    'code' => 'code',
                    'redirect_uri' => 'redirect_uri',
                ],
            ])
            ->andReturn($response = m::mock('StdClass'));
        $response
            ->shouldReceive('getBody')
            ->andReturn($accessTokenResponseBody);
        $user = $provider->user();

        $this->assertInstanceOf(SocialiteOAuth2User::class, $user);
        $this->assertEquals('foo', $user->id);
    }

    /**
     * @test
     * @expectedException \Laravel\Socialite\Two\InvalidStateException
     */
    public function exceptionIsThrownIfStateIsInvalid()
    {
        $session = m::mock(SessionInterface::class);
        $request = Request::create('foo', 'GET', [
            'state' => str_repeat('B', 40),
            'code' => 'code',
        ]);
        $request->setSession($session);
        $session
            ->shouldReceive('pull')
            ->once()
            ->with('state')
            ->andReturn(str_repeat('A', 40));
        $provider = new OAuthTwoTestProviderStub($request, 'client_id', 'client_secret', 'redirect');
        $provider->user();
    }

    /**
     * @test
     * @expectedException \Laravel\Socialite\Two\InvalidStateException
     */
    public function exceptionIsThrownIfStateIsNotSet()
    {
        $session = m::mock(SessionInterface::class);
        $request = Request::create('foo', 'GET', [
            'state' => 'state',
            'code' => 'code',
        ]);
        $request->setSession($session);
        $session
            ->shouldReceive('pull')
            ->once()
            ->with('state');
        $provider = new OAuthTwoTestProviderStub($request, 'client_id', 'client_secret', 'redirect');
        $provider->user();
    }
}

class OAuthTwoTestProviderStub extends AbstractProvider
{
    const PROVIDER_NAME = 'test';

    public $http;

    public static function providerName()
    {
        return 'test';
    }

    protected function getAuthUrl($state)
    {
        return 'http://auth.url';
    }

    protected function getTokenUrl()
    {
        return 'http://token.url';
    }

    protected function getUserByToken($token)
    {
        return ['id' => 'foo'];
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->map(['id' => $user['id']]);
    }

    /**
     * Get a fresh instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        if ($this->http) {
            return $this->http;
        }

        return $this->http = m::mock('StdClass');
    }
}
