<?php

namespace Tests\Nyholm\Psr7\Integration;

use Http\Psr7Test\StreamIntegrationTest;
use Nyholm\Psr7\Factory\StreamFactory;

class StreamTest extends StreamIntegrationTest
{
    public function createStream($data)
    {
        return (new StreamFactory())->createStream($data);
    }
}
