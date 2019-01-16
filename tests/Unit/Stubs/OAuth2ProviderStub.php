<?php

namespace SocialiteProviders\Manager\Test\Unit\Stubs;

use SocialiteProviders\Manager\OAuth2\AbstractProvider as OAuth2AbstractProvider;

class OAuth2ProviderStub extends OAuth2AbstractProvider
{
    protected $test = 'test';
    const IDENTIFIER = 'TEST';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return 'test';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->test;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        return [$this->test];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return $this->test;
    }
}
