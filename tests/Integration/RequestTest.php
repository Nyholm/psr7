<?php

namespace Tests\Nyholm\Psr7\Integration;

use Psr\Http\Message\RequestInterface;
use Http\Psr7Test\RequestIntegrationTest;
use Nyholm\Psr7\Request;

class RequestTest extends RequestIntegrationTest
{
    public function createSubject(): RequestInterface
    {
        return new Request('GET', '/');
    }
}
