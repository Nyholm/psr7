<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use Interop\Http\Factory\ServerRequestFactoryInterface;
use Nyholm\Psr7\ServerRequest;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest($method, $uri)
    {
        return new ServerRequest($method, $uri);
    }

    public function createServerRequestFromArray(array $server)
    {
        if (!isset($server['REQUEST_METHOD'])) {
            throw new \InvalidArgumentException('Cannot determine HTTP method');
        }
        // TODO: find a MUCH better way
        $method = $server['REQUEST_METHOD'];
        $SERVER = $_SERVER;
        $_SERVER = $server;
        // Until https://github.com/guzzle/psr7/pull/116 is resolved
        if (!isset($_SERVER['HTTPS'])) {
            $_SERVER['HTTPS'] = 'off';
        }
        $uri = ServerRequest::getUriFromGlobals();

        return new ServerRequest($method, $uri, [], null, '1.1', $server);
    }
}
