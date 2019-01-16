<?php

namespace SocialiteProviders\Manager\Helpers;

use SocialiteProviders\Manager\Config;
use SocialiteProviders\Manager\Contracts\Helpers\ConfigRetrieverInterface;
use SocialiteProviders\Manager\Exception\MissingConfigException;

class ServicesConfigRetriever implements ConfigRetrieverInterface
{
    /**
     * @var string
     */
    private $providerName = '';

    /**
     * @var array
     */
    private $configArray = [];

    /**
     * @param string $providerName
     * @param array  $additionalKeys
     *
     * @return \SocialiteProviders\Manager\Contracts\ConfigInterface
     *
     * @throws \SocialiteProviders\Manager\Exception\MissingConfigException
     */
    public function getConfig($providerName, array $additionalKeys = [])
    {
        $this->providerName = $providerName;
        $this->configArray = $this->getConfigArray($providerName);

        return new Config(
            $this->findOrFail('client_id'),
            $this->findOrFail('client_secret'),
            $this->findOrFail('redirect'),
            $this->findAdditionalKeys($additionalKeys)
        );
    }

    /**
     * @param array $keys
     *
     * @return array
     */
    private function findAdditionalKeys(array $keys)
    {
        if (count($keys) === 0) {
            return [];
        }

        $foundKeys = [];
        foreach ($keys as $key) {
            $foundKeys[$key] = $this->find($key);
        }

        return $foundKeys;
    }

    /**
     * @param string $key
     *
     * @return null|string
     */
    private function find($key)
    {
        return isset($this->configArray[$key]) ? $this->configArray[$key] : null;
    }

    /**
     * @param string $key
     *
     * @return string
     *
     * @throws \SocialiteProviders\Manager\Exception\MissingConfigException
     */
    private function findOrFail($key)
    {
        $value = $this->find($key);

        if (is_null($value)) {
            throw new MissingConfigException("Missing services entry for {$this->providerName}.{$key}");
        }

        return $value;
    }

    /**
     * @param string $providerName
     *
     * @return array
     */
    private function getConfigArray($providerName)
    {
        $configArray = config("services.{$providerName}");

        if (is_null($configArray)) {
            return [];
        }

        return $configArray;
    }
}
