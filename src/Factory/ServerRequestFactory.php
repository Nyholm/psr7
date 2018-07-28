<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\Factory\ServerRequestFactoryInterface;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\UploadedFile;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 */
final class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
}
