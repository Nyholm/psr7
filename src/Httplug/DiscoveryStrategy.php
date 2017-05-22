<?php

namespace Nyholm\Psr7\Httplug;

use Http\Discovery\Strategy\DiscoveryStrategy as HttplugDiscoveryStrategy;
use Nyholm\Psr7\Factory\MessageFactory;
use Nyholm\Psr7\Factory\StreamFactory;
use Nyholm\Psr7\Factory\UriFactory;

/**
 * A discovery strategy to use our factories with HTTPlug auto discovery.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class DiscoveryStrategy implements HttplugDiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [
        'Http\Message\MessageFactory' => [
            ['class' => MessageFactory::class],
        ],
        'Http\Message\StreamFactory' => [
            ['class' => StreamFactory::class],
        ],
        'Http\Message\UriFactory' => [
            ['class' => UriFactory::class],
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        if (isset(self::$classes[$type])) {
            return self::$classes[$type];
        }

        return [];
    }
}
