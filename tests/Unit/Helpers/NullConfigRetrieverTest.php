<?php

namespace SocialiteProviders\Manager\Test\Unit\Helpers;

use PHPUnit_Framework_TestCase as TestCase;
use SocialiteProviders\Manager\Helpers\NullConfigRetriever;
use SocialiteProviders\Manager\Test\Unit\ManagerTestTrait;

class NullConfigRetrieverTest extends TestCase
{
    use ManagerTestTrait;

    /**
     * @test
     */
    public function it_can_retrieve_null_config()
    {
        $retriever = new NullConfigRetriever();
        $config = $retriever->getConfig('foo');
        $this->assertSame([
            'client_id' => '',
            'client_secret' => '',
            'redirect' => '',
        ], $config->toArray());
    }

    /**
     * @test
     */
    public function it_ignores_additional_config_keys()
    {
        $retriever = new NullConfigRetriever();
        $config = $retriever->getConfig('foo', ['any', 'keys']);
        $this->assertSame([
            'client_id' => '',
            'client_secret' => '',
            'redirect' => '',
        ], $config->toArray());
    }
}