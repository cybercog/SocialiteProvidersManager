<?php

namespace SocialiteProviders\Manager\Helpers;

use SocialiteProviders\Manager\Config;
use SocialiteProviders\Manager\Contracts\Helpers\ConfigRetrieverInterface;

class NullConfigRetriever implements ConfigRetrieverInterface
{
    /**
     * @param string $providerName
     * @param array  $additionalConfigKeys
     *
     * @return \SocialiteProviders\Manager\Contracts\ConfigInterface
     */
    public function getConfig($providerName, array $additionalConfigKeys = [])
    {
        return new Config('', '', '', []);
    }
}
