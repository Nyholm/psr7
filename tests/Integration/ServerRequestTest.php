<?php declare(strict_types=1);

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\ServerRequestIntegrationTest;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestTest extends ServerRequestIntegrationTest
{
    public function createSubject(): ServerRequestInterface
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        return new ServerRequest('GET', '/', [], null, '1.1', $_SERVER);
    }
}
