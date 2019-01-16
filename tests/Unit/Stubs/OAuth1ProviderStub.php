<?php

namespace SocialiteProviders\Manager\Test\Unit\Stubs;

use SocialiteProviders\Manager\OAuth1\AbstractProvider as OAuth1AbstractProvider;

class OAuth1ProviderStub extends OAuth1AbstractProvider
{
    const IDENTIFIER = 'TEST';

    protected function mapUserToObject(array $user)
    {
        return [];
    }

    public static function additionalConfigKeys()
    {
        return [];
    }
}
