<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class StreamFactory implements \Http\Message\StreamFactory
{
    public function createStream($body = null)
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if ('resource' === gettype($body)) {
            return Stream::createFromResource($body);
        }

        return Stream::create(null === $body ? '' : $body);
    }
}
