<?php

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\ServerRequestIntegrationTest;
use Nyholm\Psr7\Factory\Psr17Factory;

class ServerRequestTest extends ServerRequestIntegrationTest
{
    public function createSubject()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        return (new Psr17Factory())->createServerRequest('GET', '/', $_SERVER);
    }
}
