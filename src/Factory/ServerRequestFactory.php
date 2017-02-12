<?php

declare(strict_types=1);

namespace Nyholm\Psr7\Factory;

use Interop\Http\Factory\ServerRequestFactoryInterface;
use Nyholm\Psr7\ServerRequest;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    public function createServerRequest(array $server, $method = null, $uri = null)
    {
        if (null === $method && isset($server['REQUEST_METHOD'])) {
            $method = $server['REQUEST_METHOD'];
        }
        if (null === $method) {
            throw new \InvalidArgumentException('Cannot determine HTTP method');
        }
        // TODO: find a MUCH better way
        if (null === $uri) {
            $SERVER = $_SERVER;
            $_SERVER = $server;
            // Until https://github.com/guzzle/psr7/pull/116 is resolved
            if (!isset($_SERVER['HTTPS'])) {
                $_SERVER['HTTPS'] = 'off';
            }
            $uri = ServerRequest::getUriFromGlobals();
            $_SERVER = $SERVER;
            unset($SERVER);
        }

        return new ServerRequest($method, $uri, [], null, '1.1', $server);
    }
}
