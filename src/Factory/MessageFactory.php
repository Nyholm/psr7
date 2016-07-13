<?php

declare (strict_types = 1);

namespace Nyholm\Psr7\Factory;

use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;

class MessageFactory implements \Http\Message\MessageFactory
{
    public function createRequest(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $protocolVersion = '1.1'
    ) {
        new Request($method, $uri, $headers, $body, $protocolVersion);
    }

    public function createResponse(
        $statusCode = 200,
        $reasonPhrase = null,
        array $headers = [],
        $body = null,
        $protocolVersion = '1.1'
    ) {
        new Response($statusCode, $headers, $body, $protocolVersion, $reasonPhrase);
    }
}
