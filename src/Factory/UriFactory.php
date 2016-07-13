<?php

declare (strict_types = 1);

namespace Nyholm\Psr7\Factory;

use Nyholm\Psr7\Uri;

class UriFactory implements \Http\Message\UriFactory
{
    public function createUri($uri)
    {
        return new Uri($uri);
    }
}
