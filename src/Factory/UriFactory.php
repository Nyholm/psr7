<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use Interop\Http\Factory\UriFactoryInterface;
use Nyholm\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class UriFactory implements \Http\Message\UriFactory, UriFactoryInterface
{
    public function createUri($uri = '')
    {
        if ($uri instanceof UriInterface) {
            return $uri;
        }

        return new Uri($uri);
    }
}
