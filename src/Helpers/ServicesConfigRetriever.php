<?php

namespace SocialiteProviders\Manager\Helpers;

use Closure;
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
     * @var string
     */
    private $providerIdentifier = '';

    /**
     * @var array
     */
    private $configArray = [];

    /**
     * @var array
     */
    private $additionalConfigKeys = [];

    /**
     * @param string $providerName
     * @param array  $additionalConfigKeys
     *
     * @return \SocialiteProviders\Manager\Contracts\ConfigInterface
     *
     * @throws \SocialiteProviders\Manager\Exception\MissingConfigException
     */
    public function getConfig($providerName, array $additionalConfigKeys = [])
    {
        $this->providerName = $providerName;
        $this->configArray = $this->getConfigArray($providerName);
        $this->additionalConfigKeys = $additionalConfigKeys;

        return new Config(
            $this->findByKey('client_id'),
            $this->findByKey('client_secret'),
            $this->findByKey('redirect'),
            $this->getConfigItems($additionalConfigKeys, function ($key) {
                return $this->findByKey(strtolower($key));
            })
        );
    }

    /**
     * @param array    $configKeys
     * @param \Closure $keyRetrievalClosure
     *
     * @return array
     */
    private function getConfigItems(array $configKeys, Closure $keyRetrievalClosure)
    {
        if (count($configKeys) === 0) {
            return [];
        }

        return $this->retrieveItemsFromConfig($configKeys, $keyRetrievalClosure);
    }

    /**
     * @param array    $keys
     * @param \Closure $keyRetrievalClosure
     *
     * @return array
     */
    private function retrieveItemsFromConfig(array $keys, Closure $keyRetrievalClosure)
    {
        $out = [];

        foreach ($keys as $key) {
            $out[$key] = $keyRetrievalClosure($key);
        }

        return $out;
    }

    /**
     * @param string $key
     *
     * @return string
     *
     * @throws \SocialiteProviders\Manager\Exception\MissingConfigException
     */
    private function findByKey($key)
    {
        $keyExists = array_key_exists($key, $this->configArray);

        // ADDITIONAL value is empty
        if (!$keyExists && $this->isAdditionalConfig($key)) {
            return;
        }

        // REQUIRED value is empty
        if (!$keyExists) {
            throw new MissingConfigException("Missing services entry for {$this->providerName}.$key");
        }

        return $this->configArray[$key];
    }

    /**
     * @param string $providerName
     *
     * @return array
     *
     * @throws \SocialiteProviders\Manager\Exception\MissingConfigException
     */
    private function getConfigArray($providerName)
    {
        $configArray = config("services.{$providerName}");

        if (empty($configArray)) {
            // If we are running in console we should spoof values to make Socialite happy...
            if (app()->runningInConsole()) {
                $configArray = [
                    'client_id' => "{$this->providerIdentifier}_KEY",
                    'client_secret' => "{$this->providerIdentifier}_SECRET",
                    'redirect' => "{$this->providerIdentifier}_REDIRECT_URI",
                ];
            } else {
                throw new MissingConfigException("There is no services entry for $providerName");
            }
        }

        return $configArray;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    private function isAdditionalConfig($key)
    {
        return in_array(strtolower($key), $this->additionalConfigKeys, true);
    }
}
