<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use Nyholm\Psr7\UploadedFile;
use Nyholm\Psr7\Uri;
use Psr\Http\Message as Psr;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 */
final class Psr17Factory implements Psr\RequestFactoryInterface, Psr\ResponseFactoryInterface, Psr\ServerRequestFactoryInterface, Psr\StreamFactoryInterface, Psr\UploadedFileFactoryInterface, Psr\UriFactoryInterface
{
    public function createRequest(string $method, $uri): Psr\RequestInterface
    {
        return new Request($method, $uri);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): Psr\ResponseInterface
    {
        return new Response($code, [], null, '1.1', $reasonPhrase);
    }

    public function createStream(string $content = ''): Psr\StreamInterface
    {
        return Stream::create($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): Psr\StreamInterface
    {
        return Stream::create(fopen($filename, $mode));
    }

    public function createStreamFromResource($resource): Psr\StreamInterface
    {
        return Stream::create($resource);
    }

    public function createUploadedFile(Psr\StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null): Psr\UploadedFileInterface
    {
        if (null === $size) {
            $size = $stream->getSize();
        }

        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    public function createUri(string $uri = ''): Psr\UriInterface
    {
        return new Uri($uri);
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): Psr\ServerRequestInterface
    {
        return new ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
}
