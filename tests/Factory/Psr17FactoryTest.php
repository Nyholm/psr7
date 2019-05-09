<?php

namespace Tests\Nyholm\Psr7\Factory;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Psr17FactoryTest extends TestCase
{
    public function testCreateRequest()
    {
        $factory = new Psr17Factory();
        $r = $factory->createRequest('POST', 'https://nyholm.tech');

        $this->assertEquals('POST', $r->getMethod());
        $this->assertEquals('https://nyholm.tech', $r->getUri()->__toString());

        $headers = $r->getHeaders();
        $this->assertCount(1, $headers); // Including HOST
    }

    public function testCreateResponse()
    {
        $factory = new Psr17Factory();
        $usual = $factory->createResponse(404);
        $this->assertEquals(404, $usual->getStatusCode());
        $this->assertEquals('Not Found', $usual->getReasonPhrase());

        $r = $factory->createResponse(217, 'Perfect');

        $this->assertEquals(217, $r->getStatusCode());
        $this->assertEquals('Perfect', $r->getReasonPhrase());
    }

    public function testCreateStream()
    {
        $factory = new Psr17Factory();
        $stream = $factory->createStream('foobar');

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals('foobar', $stream->__toString());
    }

    public function testCreateUri()
    {
        $factory = new Psr17Factory();
        $uri = $factory->createUri('https://nyholm.tech/foo');

        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('https://nyholm.tech/foo', $uri->__toString());
    }
}
