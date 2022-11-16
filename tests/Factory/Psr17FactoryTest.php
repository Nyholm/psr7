<?php

declare(strict_types=1);

namespace Tests\Nyholm\Psr7\Factory;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

class Psr17FactoryTest extends TestCase
{
    public function testCreateResponse()
    {
        $factory = new Psr17Factory();
        $r = $factory->createResponse(200);
        $this->assertEquals('OK', $r->getReasonPhrase());

        $r = $factory->createResponse(200, '');
        $this->assertEquals('', $r->getReasonPhrase());

        $r = $factory->createResponse(200, 'Foo');
        $this->assertEquals('Foo', $r->getReasonPhrase());

        /*
         * Test for non-standard response codes
         */
        $r = $factory->createResponse(567);
        $this->assertEquals('', $r->getReasonPhrase());

        $r = $factory->createResponse(567, '');
        $this->assertEquals(567, $r->getStatusCode());
        $this->assertEquals('', $r->getReasonPhrase());

        $r = $factory->createResponse(567, 'Foo');
        $this->assertEquals(567, $r->getStatusCode());
        $this->assertEquals('Foo', $r->getReasonPhrase());
    }

    public function testCreateRequest()
    {
        $factory = new Psr17Factory();
        $req = $factory->createRequest('POST', 'https://nyholm.tech');
        $body = $factory->createStream('Foo');
        $req = $req->withBody($body);
        $this->assertEquals('Foo', $req->getBody()->getContents());
        $this->assertEquals('', $req->getBody()->getContents());
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('https://nyholm.tech', $req->getUri());

        $req = $factory->createRequest('POST', 'https://nyholm.tech');
        $body = $factory->createStream('');
        $req = $req->withBody($body);
        $this->assertEquals('', $req->getBody()->getContents());
        $this->assertEquals('', $req->getBody()->getContents());
        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('https://nyholm.tech', $req->getUri());
    }
}
