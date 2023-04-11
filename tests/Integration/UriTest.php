<?php declare(strict_types=1);

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\UriIntegrationTest;
use Nyholm\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class UriTest extends UriIntegrationTest
{
    public function createUri($uri): UriInterface
    {
        return new Uri($uri);
    }
}
