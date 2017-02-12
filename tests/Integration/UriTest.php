<?php

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\UriIntegrationTest;
use Nyholm\Psr7\Factory\UriFactory;

class UriTest extends UriIntegrationTest
{
    public function createUri($uri)
    {
        return (new UriFactory())->createUri($uri);
    }
}
