<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Interop\Http\Factory\ServerRequestFactoryInterface;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\UploadedFile;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 *
 * @internal This class does not fall under our BC promise. We will adapt to changes to the http-interop/http-factory.
 * This class will be finalized when the PSR-17 is accepted.
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest($method, $uri): ServerRequestInterface
    {
        return new ServerRequest($method, $uri);
    }

    public function createServerRequestFromArray(array $server): ServerRequestInterface
    {
        return new ServerRequest(
            $this->getMethodFromEnvironment($server),
            $this->getUriFromEnvironmentWithHTTP($server),
            [],
            null,
            '1.1',
            $server
        );
    }

    /**
     * Create a new server request from a set of arrays.
     *
     * @param array $server  Typically $_SERVER or similar structure.
     * @param array $headers Typically the output of getallheaders() or similar structure.
     * @param array $cookie  Typically $_COOKIE or similar structure.
     * @param array $get     Typically $_GET or similar structure.
     * @param array $post    Typically $_POST or similar structure.
     * @param array $files   Typically $_FILES or similar structure.
     *
     * @throws InvalidArgumentException If no valid method or URI can be determined.
     *
     * @return ServerRequestInterface
     */
    public function createServerRequestFromArrays(
        array $server,
        array $headers,
        array $cookie,
        array $get,
        array $post,
        array $files
    ): ServerRequestInterface {
        $method = $this->getMethodFromEnvironment($server);
        $uri = $this->getUriFromEnvironmentWithHTTP($server);

        $protocol = isset($server['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $server['SERVER_PROTOCOL']) : '1.1';

        $serverRequest = new ServerRequest($method, $uri, $headers, null, $protocol, $server);

        return $serverRequest
            ->withCookieParams($cookie)
            ->withQueryParams($get)
            ->withParsedBody($post)
            ->withUploadedFiles(self::normalizeFiles($files));
    }

    /**
     * Create a new server request from the current environment variables.
     * Defaults to a GET request to minimise the risk of an InvalidArgumentException.
     * Includes the current request headers as supplied by the server through `getallheaders()`.
     *
     * @throws InvalidArgumentException If no valid method or URI can be determined.
     *
     * @return ServerRequestInterface
     */
    public function createServerRequestFromGlobals(): ServerRequestInterface
    {
        $server = $_SERVER;
        if (false === isset($server['REQUEST_METHOD'])) {
            $server['REQUEST_METHOD'] = 'GET';
        }
        $headers = function_exists('getallheaders') ? getallheaders() : [];

        return $this->createServerRequestFromArrays($_SERVER, $headers, $_COOKIE, $_GET, $_POST, $_FILES);
    }

    private function getMethodFromEnvironment(array $environment): string
    {
        if (false === isset($environment['REQUEST_METHOD'])) {
            throw new InvalidArgumentException('Cannot determine HTTP method');
        }

        return $environment['REQUEST_METHOD'];
    }

    private function getUriFromEnvironmentWithHTTP(array $environment): \Psr\Http\Message\UriInterface
    {
        $uri = (new UriFactory())->createUriFromArray($environment);
        if ('' === $uri->getScheme()) {
            $uri = $uri->withScheme('http');
        }

        return $uri;
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
