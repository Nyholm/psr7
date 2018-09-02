<?php

declare(strict_types=1);

namespace Nyholm\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * @author Michael Dowling and contributors to guzzlehttp/psr7
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 */
final class Stream implements StreamInterface
{
    /** @var resource A resource reference */
    private $stream;

    /** @var bool */
    private $seekable;

    /** @var bool */
    private $readable;

    /** @var bool */
    private $writable;

    /** @var array|mixed|null|void */
    private $uri;

    /** @var int */
    private $size;

    /** @var array Hash of readable and writable stream types */
    private static $readWriteHash = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true,
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true,
        ],
    ];

    private function __construct()
    {
    }

    /**
     * Creates a new PSR-7 stream.
     *
     * @param string|resource|StreamInterface $body
     *
     * @return StreamInterface
     *
     * @throws \InvalidArgumentException
     */
    public static function create($body = ''): StreamInterface
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if (\is_string($body)) {
            $resource = \fopen('php://temp', 'rw+');
            \fwrite($resource, $body);
            $body = $resource;
        }

        if ('resource' === \gettype($body)) {
            $obj = new self();
            $obj->stream = $body;
            $meta = \stream_get_meta_data($obj->stream);
            $obj->seekable = $meta['seekable'];
            $obj->readable = isset(self::$readWriteHash['read'][$meta['mode']]);
            $obj->writable = isset(self::$readWriteHash['write'][$meta['mode']]);
            $obj->uri = $obj->getMetadata('uri');

            return $obj;
        }

        throw new \InvalidArgumentException('First argument to Stream::create() must be a string, resource or StreamInterface.');
    }

    /**
     * Closes the stream when the destructed.
     */
    public function __destruct()
    {
        $this->close();
    }

    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }

            return $this->getContents();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function close(): void
    {
        if (isset($this->stream)) {
            if (\is_resource($this->stream)) {
                \fclose($this->stream);
            }
            $this->detach();
        }
    }

    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }

        $result = $this->stream;
        unset($this->stream);
        $this->size = $this->uri = null;
        $this->readable = $this->writable = $this->seekable = false;

        return $result;
    }

    public function getSize(): ?int
    {
        if (null !== $this->size) {
            return $this->size;
        }

        if (!isset($this->stream)) {
            return null;
        }

        // Clear the stat cache if the stream has a URI
        if ($this->uri) {
            \clearstatcache(true, $this->uri);
        }

        $stats = \fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];

            return $this->size;
        }

        return null;
    }

    public function tell(): int
    {
        if (false === $result = \ftell($this->stream)) {
            throw new \RuntimeException('Unable to determine stream position');
        }

        return $result;
    }

    public function eof(): bool
    {
        return !$this->stream || \feof($this->stream);
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function seek($offset, $whence = \SEEK_SET): void
    {
        if (!$this->seekable) {
            throw new \RuntimeException('Stream is not seekable');
        } elseif (\fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException('Unable to seek to stream position '.$offset.' with whence '.\var_export($whence, true));
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write($string): int
    {
        if (!$this->writable) {
            throw new \RuntimeException('Cannot write to a non-writable stream');
        }

        // We can't know the size after writing anything
        $this->size = null;

        if (false === $result = \fwrite($this->stream, $string)) {
            throw new \RuntimeException('Unable to write to stream');
        }

        return $result;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read($length): string
    {
        if (!$this->readable) {
            throw new \RuntimeException('Cannot read from non-readable stream');
        }

        return \fread($this->stream, $length);
    }

    public function getContents(): string
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException('Unable to read stream contents');
        }

        if (false === $contents = \stream_get_contents($this->stream)) {
            throw new \RuntimeException('Unable to read stream contents');
        }

        return $contents;
    }

    public function getMetadata($key = null)
    {
        if (!isset($this->stream)) {
            return $key ? null : [];
        } elseif (null === $key) {
            return \stream_get_meta_data($this->stream);
        }

        $meta = \stream_get_meta_data($this->stream);

        return isset($meta[$key]) ? $meta[$key] : null;
    }
}
