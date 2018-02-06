<?php

declare(strict_types=1);

namespace Nyholm\Psr7;

use InvalidArgumentException;
use Nyholm\Psr7\Factory\StreamFactory;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

/**
 * @author Michael Dowling and contributors to guzzlehttp/psr7
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class UploadedFile implements UploadedFileInterface
{
    /** @var int[] */
    private static $errors = [
        UPLOAD_ERR_OK, UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE, UPLOAD_ERR_PARTIAL, UPLOAD_ERR_NO_FILE,
        UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION,
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
    public function __construct(
        $streamOrFile,
        $size,
        $errorStatus,
        $clientFilename = null,
        $clientMediaType = null
    ) {
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
     * @param mixed $streamOrFile
     *
     * @throws InvalidArgumentException
     */
    private function setStreamOrFile($streamOrFile): void
    {
        if (is_string($streamOrFile)) {
            $this->file = $streamOrFile;
        } elseif (is_resource($streamOrFile)) {
            $this->stream = Stream::createFromResource($streamOrFile);
        } elseif ($streamOrFile instanceof StreamInterface) {
            $this->stream = $streamOrFile;
        } else {
            throw new InvalidArgumentException('Invalid stream or file provided for UploadedFile');
        }
    }

    /**
     * @param int $error
     *
     * @throws InvalidArgumentException
     */
    private function setError($error): void
    {
        if (false === is_int($error)) {
            throw new InvalidArgumentException('Upload file error status must be an integer');
        }

        if (false === in_array($error, self::$errors)) {
            throw new InvalidArgumentException('Invalid error status for UploadedFile');
        }

        $this->error = $error;
    }

    /**
     * @param int $size
     *
     * @throws InvalidArgumentException
     */
    private function setSize($size): void
    {
        if (false === is_int($size)) {
            throw new InvalidArgumentException('Upload file size must be an integer');
        }

        $this->size = $size;
    }

    private function isStringOrNull($param): bool
    {
        return in_array(gettype($param), ['string', 'NULL']);
    }

    private function isStringNotEmpty($param): bool
    {
        return is_string($param) && false === empty($param);
    }

    private function setClientFilename($clientFilename): void
    {
        if (false === $this->isStringOrNull($clientFilename)) {
            throw new InvalidArgumentException('Upload file client filename must be a string or null');
        }

        $this->clientFilename = $clientFilename;
    }

    private function setClientMediaType($clientMediaType): void
    {
        if (false === $this->isStringOrNull($clientMediaType)) {
            throw new InvalidArgumentException('Upload file client media type must be a string or null');
        }

        $this->clientMediaType = $clientMediaType;
    }

    /**
     * @return bool Return true if there is no upload error.
     */
    private function isOk(): bool
    {
        return UPLOAD_ERR_OK === $this->error;
    }

    /**
     * @throws RuntimeException if is moved or not ok
     */
    private function validateActive(): void
    {
        if (false === $this->isOk()) {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }

        if ($this->moved) {
            throw new RuntimeException('Cannot retrieve stream after it has already been moved');
        }
    }

    public function getStream(): StreamInterface
    {
        $this->validateActive();

        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }

        $resource = fopen($this->file, 'r');

        return Stream::createFromResource($resource);
    }

    public function moveTo($targetPath): void
    {
        $this->validateActive();

        if (false === $this->isStringNotEmpty($targetPath)) {
            throw new InvalidArgumentException('Invalid path provided for move operation; must be a non-empty string');
        }

        if (null !== $this->file) {
            $this->moved = 'cli' == php_sapi_name()
                ? rename($this->file, $targetPath)
                : move_uploaded_file($this->file, $targetPath);
        } else {
            $stream = $this->getStream();
            if ($stream->isSeekable()) {
                $stream->rewind();
            }
            (new StreamFactory())->copyToStream(
                $stream,
                Stream::createFromResource(fopen($targetPath, 'w'))
            );

            $this->moved = true;
        }

        if (false === $this->moved) {
            throw new RuntimeException(sprintf('Uploaded file could not be moved to %s', $targetPath));
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
}
