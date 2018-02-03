<?php

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\ServerRequestIntegrationTest;
use Nyholm\Psr7\Factory\ServerRequestFactory;

class ServerRequestTest extends ServerRequestIntegrationTest
{
    public function createSubject()
    {
        return (new ServerRequestFactory())->createServerRequest('GET', '/');
    }
}
