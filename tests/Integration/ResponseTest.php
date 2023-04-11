<?php declare(strict_types=1);

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\ResponseIntegrationTest;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends ResponseIntegrationTest
{
    public function createSubject(): ResponseInterface
    {
        return new Response();
    }
}
