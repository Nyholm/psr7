<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use Nyholm\Psr7\Stream;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class StreamFactory implements \Http\Message\StreamFactory
{
    public function createStream($body = null)
    {
        return Stream::create(null === $body ? '' : $body);
    }
}
