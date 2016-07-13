<?php
declare(strict_types=1);
namespace Nyholm\Psr7\Factory;


use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

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
        new Response($statusCode, $reasonPhrase, $headers, $body, $protocolVersion);
    }

}