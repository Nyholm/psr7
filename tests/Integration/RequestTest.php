<?php declare(strict_types=1);

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\RequestIntegrationTest;
use Nyholm\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class RequestTest extends RequestIntegrationTest
{
    public function createSubject(): RequestInterface
    {
        return new Request('GET', '/');
    }
}
