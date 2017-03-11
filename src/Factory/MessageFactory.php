<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use Interop\Http\Factory\RequestFactoryInterface;
use Interop\Http\Factory\ResponseFactoryInterface;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MessageFactory implements \Http\Message\MessageFactory, RequestFactoryInterface, ResponseFactoryInterface
{
    public function createRequest(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $protocolVersion = '1.1'
    ) {
        return new Request($method, $uri, $headers, $body, $protocolVersion);
    }

    public function createResponse(
        $statusCode = 200,
        $reasonPhrase = null,
        array $headers = [],
        $body = null,
        $protocolVersion = '1.1'
    ) {
        return new Response((int) $statusCode, $headers, $body, $protocolVersion, $reasonPhrase);
    }
}
