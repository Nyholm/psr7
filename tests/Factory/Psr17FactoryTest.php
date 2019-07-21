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
    }
}
