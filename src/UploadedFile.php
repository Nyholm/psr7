<?php

declare(strict_types=1);

namespace Nyholm\Psr7;

use Psr\Http\Message\{StreamInterface, UploadedFileInterface};

/**
 * @author Michael Dowling and contributors to guzzlehttp/psr7
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 */
final class UploadedFile implements UploadedFileInterface
{
    /** @var int[] */
    private static $errors = [
        \UPLOAD_ERR_OK, \UPLOAD_ERR_INI_SIZE, \UPLOAD_ERR_FORM_SIZE, \UPLOAD_ERR_PARTIAL, \UPLOAD_ERR_NO_FILE,
        \UPLOAD_ERR_NO_TMP_DIR, \UPLOAD_ERR_CANT_WRITE, \UPLOAD_ERR_EXTENSION,
    ];

    /** @var string */
    private $clientFilename;

    /** @var string */
    private $clientMediaType;

    /** @var int */
    private $error;

    /** @var null|string */
    private $file;

    /** @var bool */
    private $moved = false;

    /** @var null|int */
    private $size;

    /** @var null|StreamInterface */
    private $stream;

    /**
     * @param StreamInterface|string|resource $streamOrFile
     * @param int                             $size
     * @param int                             $errorStatus
     * @param string|null                     $clientFilename
     * @param string|null                     $clientMediaType
     */
    public function __construct($streamOrFile, $size, $errorStatus, $clientFilename = null, $clientMediaType = null)
    {
        $this->setError($errorStatus);
        $this->setSize($size);
        $this->setClientFilename($clientFilename);
        $this->setClientMediaType($clientMediaType);

        if ($this->isOk()) {
            $this->setStreamOrFile($streamOrFile);
        }
    }

    /**
     * Depending on the value set file or stream variable.
     *
     * @param string|resource|StreamInterface $streamOrFile
     *
     * @throws \InvalidArgumentException
     */
    private function setStreamOrFile($streamOrFile): void
    {
        if (\is_string($streamOrFile)) {
            $this->file = $streamOrFile;
        } elseif (\is_resource($streamOrFile)) {
            $this->stream = Stream::create($streamOrFile);
        } elseif ($streamOrFile instanceof StreamInterface) {
            $this->stream = $streamOrFile;
        } else {
            throw new \InvalidArgumentException('Invalid stream or file provided for UploadedFile');
        }
    }

    private function setError($error): void
    {
        if (false === \is_int($error)) {
            throw new \InvalidArgumentException('Upload file error status must be an integer');
        }

        if (false === \in_array($error, self::$errors)) {
            throw new \InvalidArgumentException('Invalid error status for UploadedFile');
        }

        $this->error = $error;
    }

    private function setSize($size): void
    {
        if (false === \is_int($size)) {
            throw new \InvalidArgumentException('Upload file size must be an integer');
        }

        $this->size = $size;
    }

    private function isStringOrNull($param): bool
    {
        return \in_array(\gettype($param), ['string', 'NULL']);
    }

    private function isStringNotEmpty($param): bool
    {
        return \is_string($param) && false === empty($param);
    }

    private function setClientFilename($clientFilename): void
    {
        if (false === $this->isStringOrNull($clientFilename)) {
            throw new \InvalidArgumentException('Upload file client filename must be a string or null');
        }

        $this->clientFilename = $clientFilename;
    }

    private function setClientMediaType($clientMediaType): void
    {
        if (false === $this->isStringOrNull($clientMediaType)) {
            throw new \InvalidArgumentException('Upload file client media type must be a string or null');
        }

        $this->clientMediaType = $clientMediaType;
    }

    /**
     * @return bool return true if there is no upload error
     */
    private function isOk(): bool
    {
        return \UPLOAD_ERR_OK === $this->error;
    }

    /**
     * @throws \RuntimeException if is moved or not ok
     */
    private function validateActive(): void
    {
        if (false === $this->isOk()) {
            throw new \RuntimeException('Cannot retrieve stream due to upload error');
        }

        if ($this->moved) {
            throw new \RuntimeException('Cannot retrieve stream after it has already been moved');
        }
    }

    public function getStream(): StreamInterface
    {
        $this->validateActive();

        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }

        $resource = \fopen($this->file, 'r');

        return Stream::create($resource);
    }

    public function moveTo($targetPath): void
    {
        $this->validateActive();

        if (false === $this->isStringNotEmpty($targetPath)) {
            throw new \InvalidArgumentException('Invalid path provided for move operation; must be a non-empty string');
        }

        if (null !== $this->file) {
            $this->moved = 'cli' === PHP_SAPI ? \rename($this->file, $targetPath) : \move_uploaded_file($this->file, $targetPath);
        } else {
            $stream = $this->getStream();
            if ($stream->isSeekable()) {
                $stream->rewind();
            }
            $this->copyToStream($stream, Stream::create(\fopen($targetPath, 'w')));
            $this->moved = true;
        }

        if (false === $this->moved) {
            throw new \RuntimeException(\sprintf('Uploaded file could not be moved to %s', $targetPath));
        }
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
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
    private function copyToStream(StreamInterface $source, StreamInterface $dest, $maxLen = -1)
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
            if (!($len = \strlen($buf))) {
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
