<?php declare(strict_types=1);

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\StreamIntegrationTest;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

class StreamTest extends StreamIntegrationTest
{
    public function createStream($data): StreamInterface
    {
        return Stream::create($data);
    }
}
