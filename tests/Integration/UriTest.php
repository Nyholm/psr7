<?php

namespace Tests\Nyholm\Psr7\Integration;

use Psr\Http\Message\UriInterface;
use Http\Psr7Test\UriIntegrationTest;
use Nyholm\Psr7\Uri;

class UriTest extends UriIntegrationTest
{
    public function createUri($uri): UriInterface
    {
        return new Uri($uri);
    }
}
