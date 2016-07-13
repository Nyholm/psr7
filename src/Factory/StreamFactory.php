<?php

declare (strict_types = 1);

namespace Nyholm\Psr7\Factory;

use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

/**
 * @author Michael Dowling and contributors to guzzlehttp/psr7
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

    /**
     * Copy the contents of a stream into another stream until the given number
     * of bytes have been read.
     *
     * @param StreamInterface $source Stream to read from
     * @param StreamInterface $dest   Stream to write to
     * @param int             $maxLen Maximum number of bytes to read. Pass -1
     *                                to read the entire stream.
     *
     * @throws \RuntimeException on error.
     */
    public function copyToStream(StreamInterface $source, StreamInterface $dest, $maxLen = -1)
    {
        if ($maxLen === -1) {
            while (!$source->eof()) {
                if (!$dest->write($source->read(1048576))) {
                    break;
                }
            }
            return;
        }

        $bytes = 0;
        while (!$source->eof()) {
            $buf = $source->read($maxLen - $bytes);
            if (!($len = strlen($buf))) {
                break;
            }
            $bytes += $len;
            $dest->write($buf);
            if ($bytes == $maxLen) {
                break;
            }
        }
    }
}
