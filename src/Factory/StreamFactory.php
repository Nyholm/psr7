<?php

declare (strict_types = 1);

namespace Nyholm\Psr7\Factory;

use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class StreamFactory implements \Http\Message\StreamFactory
{
    public function createStream($body = null)
    {
        if (is_scalar($body)) {
            // Copy to our stream
            $stream = fopen('php://temp', 'r+');
            if ($body !== '') {
                fwrite($stream, $body);
                fseek($stream, 0);
            }

            return new Stream($stream);
        }

        switch (gettype($body)) {
            case 'resource':
                return new Stream($body);
            case 'object':
                if ($body instanceof StreamInterface) {
                    return $body;
                } elseif (method_exists($body, '__toString')) {
                    return $this->createStream((string) $body);
                }
                break;
            case 'NULL':
                return new Stream(fopen('php://temp', 'r+'));
        }

        throw new \InvalidArgumentException('Invalid resource type: '.gettype($body));
    }
}
