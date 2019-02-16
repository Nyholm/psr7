<?php

declare(strict_types=1);

namespace Nyholm\Psr7;

use Psr\Http\Message\{RequestInterface, StreamInterface, UriInterface};

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 */
final class Request implements RequestInterface
{
    use MessageTrait;
    use RequestTrait;

    /**
     * @param string $method HTTP method
     * @param string|UriInterface $uri URI
     * @param array $headers Request headers
     * @param string|resource|StreamInterface|null $body Request body
     * @param string $version Protocol version
     */
    public function __construct(string $method, $uri, array $headers = [], $body = null, string $version = '1.1')
    {
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }

        $this->method = $method;
        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $version;

        if (!$this->hasHeader('Host')) {
            $this->updateHostFromUri();
        }

        // If we got no body, defer initialization of the stream until Request::getBody()
        if ('' !== $body && null !== $body) {
            $this->stream = Stream::create($body);
        }
    }
}
