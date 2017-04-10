<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Interop\Http\Factory\ServerRequestFactoryInterface;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Factory\UriFactory;
use Nyholm\Psr7\UploadedFile;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest($method, $uri): ServerRequestInterface
    {
        return new ServerRequest($method, $uri);
    }

    public function createServerRequestFromArray(array $server): ServerRequestInterface
    {
        if (!isset($server['REQUEST_METHOD'])) {
            throw new InvalidArgumentException('Cannot determine HTTP method');
        }
        $method = $server['REQUEST_METHOD'];

        $uri = (new UriFactory())->createUriFromArray($server);
        if ($uri->getScheme() === '') {
            $uri = $uri->withScheme('http');
        }

        $protocol = isset($server['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $server['SERVER_PROTOCOL']) : '1.1';

        return new ServerRequest($method, $uri, [], null, $protocol, $server);
    }

    /**
     * Create a new server request from a set of arrays.
     *
     * @param array $server Typically $_SERVER or similar structure.
     * @param array $cookie Typically $_COOKIE or similar structure.
     * @param array $get    Typically $_GET or similar structure.
     * @param array $post   Typically $_POST or similar structure.
     * @param array $files  Typically $_FILES or similar structure.
     *
     * @throws InvalidArgumentException If no valid method or URI can be determined.
     *
     * @return ServerRequestInterface
     */
    public function createServerRequestFromArrays(
        array $server,
        array $cookie,
        array $get,
        array $post,
        array $files
    ): ServerRequestInterface {
        if (!isset($server['REQUEST_METHOD'])) {
            throw new InvalidArgumentException('Cannot determine HTTP method');
        }
        $method = $server['REQUEST_METHOD'];

        $uri = (new UriFactory())->createUriFromArray($server);
        if ($uri->getScheme() === '') {
            $uri = $uri->withScheme('http');
        }

        $protocol = isset($server['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $server['SERVER_PROTOCOL']) : '1.1';

        $headers = function_exists('getallheaders') ? getallheaders() : [];

        $serverRequest = new ServerRequest($method, $uri, $headers, null, $protocol, $server);

        return $serverRequest
            ->withCookieParams($cookie)
            ->withQueryParams($get)
            ->withParsedBody($post)
            ->withUploadedFiles(self::normalizeFiles($files));
    }

    /**
     * Create a new server request from the current environment variables.
     *
     * @return ServerRequestInterface
     */
    public function createServerRequestFromGlobals(): ServerRequestInterface
    {
        $server = $_SERVER;
        if (false === isset($server['REQUEST_METHOD'])) {
            $server['REQUEST_METHOD'] = 'GET';
        }
        return $this->createServerRequestFromArrays($_SERVER, $_COOKIE, $_GET, $_POST, $_FILES);
    }

    /**
     * Return an UploadedFile instance array.
     *
     * @param array $files A array which respect $_FILES structure
     *
     * @throws InvalidArgumentException for unrecognized values
     *
     * @return array
     */
    private static function normalizeFiles(array $files): array
    {
        $normalized = [];

        foreach ($files as $key => $value) {
            if ($value instanceof UploadedFileInterface) {
                $normalized[$key] = $value;
            } elseif (is_array($value) && isset($value['tmp_name'])) {
                $normalized[$key] = self::createUploadedFileFromSpec($value);
            } elseif (is_array($value)) {
                $normalized[$key] = self::normalizeFiles($value);
                continue;
            } else {
                throw new InvalidArgumentException('Invalid value in files specification');
            }
        }

        return $normalized;
    }

    /**
     * Create and return an UploadedFile instance from a $_FILES specification.
     *
     * If the specification represents an array of values, this method will
     * delegate to normalizeNestedFileSpec() and return that return value.
     *
     * @param array $value $_FILES struct
     *
     * @return array|UploadedFileInterface
     */
    private static function createUploadedFileFromSpec(array $value)
    {
        if (is_array($value['tmp_name'])) {
            return self::normalizeNestedFileSpec($value);
        }

        return new UploadedFile(
            $value['tmp_name'],
            (int) $value['size'],
            (int) $value['error'],
            $value['name'],
            $value['type']
        );
    }

    /**
     * Normalize an array of file specifications.
     *
     * Loops through all nested files and returns a normalized array of
     * UploadedFileInterface instances.
     *
     * @param array $files
     *
     * @return UploadedFileInterface[]
     */
    private static function normalizeNestedFileSpec(array $files = []): array
    {
        $normalizedFiles = [];

        foreach (array_keys($files['tmp_name']) as $key) {
            $spec = [
                'tmp_name' => $files['tmp_name'][$key],
                'size' => $files['size'][$key],
                'error' => $files['error'][$key],
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
            ];
            $normalizedFiles[$key] = self::createUploadedFileFromSpec($spec);
        }

        return $normalizedFiles;
    }
}
