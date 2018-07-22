<?php

declare(strict_types=1);

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
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if ('resource' === gettype($body)) {
            return Stream::createFromResource($body);
        }

        return Stream::create(null === $body ? '' : $body);
    }

    /**
     * Copy the contents of a stream into another stream until the given number
     * of bytes have been read.
     *
     * @author Michael Dowling and contributors to guzzlehttp/psr7
     *
     * @param StreamInterface $source Stream to read from
     * @param StreamInterface $dest   Stream to write to
     * @param int             $maxLen Maximum number of bytes to read. Pass -1
     *                                to read the entire stream
     *
     * @throws \RuntimeException on error
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
            if ($bytes === $maxLen) {
                break;
            }
        }
    }
}
