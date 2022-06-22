<?php

namespace Tests\Nyholm\Psr7\Integration;

use Psr\Http\Message\ServerRequestInterface;
use Http\Psr7Test\ServerRequestIntegrationTest;
use Nyholm\Psr7\ServerRequest;

class ServerRequestTest extends ServerRequestIntegrationTest
{
    public function createSubject(): ServerRequestInterface
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        return new ServerRequest('GET', '/', [], null, '1.1', $_SERVER);
    }
}
