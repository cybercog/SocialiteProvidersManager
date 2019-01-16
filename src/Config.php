<?php

namespace SocialiteProviders\Manager;

use SocialiteProviders\Manager\Contracts\ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Config constructor.
     *
     * @param string $key
     * @param string $secret
     * @param string $callbackUri
     * @param array  $additionalProviderConfig
     */
    public function __construct($key, $secret, $callbackUri, array $additionalProviderConfig = [])
    {
        $this->config = array_merge([
            'client_id' => $key,
            'client_secret' => $secret,
            'redirect' => $callbackUri,
        ], $additionalProviderConfig);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->config;
    }
}
