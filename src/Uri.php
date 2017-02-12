<?php

declare(strict_types=1);

namespace Nyholm\Psr7;

use Psr\Http\Message\UriInterface;

/**
 * PSR-7 URI implementation.
 *
 * @author Michael Dowling
 * @author Tobias Schultze
 * @author Matthew Weier O'Phinney
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Uri implements UriInterface
{
    private static $schemes = [
        'http'  => 80,
        'https' => 443,
    ];

    private static $charUnreserved = 'a-zA-Z0-9_\-\.~';
    private static $charSubDelims = '!\$&\'\(\)\*\+,;=';
    private static $replaceQuery = ['=' => '%3D', '&' => '%26'];

    /**
     * @var string Uri scheme.
     */
    private $scheme = '';

    /**
     * @var string Uri user info.
     */
    private $userInfo = '';

    /**
     * @var string Uri host.
     */
    private $host = '';

    /**
     * @var int|null Uri port.
     */
    private $port;

    /**
     * @var string Uri path.
     */
    private $path = '';

    /**
     * @var string Uri query string.
     */
    private $query = '';

    /**
     * @var string Uri fragment.
     */
    private $fragment = '';

    /**
     * @param string $uri
     */
    public function __construct($uri = '')
    {
        if ($uri != '') {
            $parts = parse_url($uri);
            if ($parts === false) {
                throw new \InvalidArgumentException("Unable to parse URI: $uri");
            }

            $this->applyParts($parts);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return self::createUriString(
            $this->scheme,
            $this->getAuthority(),
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority(): string
    {
        if ($this->host == '') {
            return '';
        }

        $authority = $this->host;
        if ($this->userInfo != '') {
            $authority = $this->userInfo.'@'.$authority;
        }

        if ($this->port !== null) {
            $authority .= ':'.$this->port;
        }

        return $authority;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme): self
    {
        $scheme = $this->filterScheme($scheme);

        if ($this->scheme === $scheme) {
            return $this;
        }

        $new = clone $this;
        $new->scheme = $scheme;
        $new->port = $new->filterPort($new->port);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null): self
    {
        $info = $user;
        if ($password != '') {
            $info .= ':'.$password;
        }

        if ($this->userInfo === $info) {
            return $this;
        }

        $new = clone $this;
        $new->userInfo = $info;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host): self
    {
        $host = $this->filterHost($host);

        if ($this->host === $host) {
            return $this;
        }

        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port): self
    {
        $port = $this->filterPort($port);

        if ($this->port === $port) {
            return $this;
        }

        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path): self
    {
        $path = $this->filterPath($path);

        if ($this->path === $path) {
            return $this;
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query): self
    {
        $query = $this->filterQueryAndFragment($query);

        if ($this->query === $query) {
            return $this;
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment): self
    {
        $fragment = $this->filterQueryAndFragment($fragment);

        if ($this->fragment === $fragment) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    /**
     * Apply parse_url parts to a URI.
     *
     * @param array $parts Array of parse_url parts to apply.
     */
    private function applyParts(array $parts)
    {
        $this->scheme = isset($parts['scheme'])
            ? $this->filterScheme($parts['scheme'])
            : '';
        $this->userInfo = isset($parts['user']) ? $parts['user'] : '';
        $this->host = isset($parts['host'])
            ? $this->filterHost($parts['host'])
            : '';
        $this->port = isset($parts['port'])
            ? $this->filterPort($parts['port'])
            : null;
        $this->path = isset($parts['path'])
            ? $this->filterPath($parts['path'])
            : '';
        $this->query = isset($parts['query'])
            ? $this->filterQueryAndFragment($parts['query'])
            : '';
        $this->fragment = isset($parts['fragment'])
            ? $this->filterQueryAndFragment($parts['fragment'])
            : '';
        if (isset($parts['pass'])) {
            $this->userInfo .= ':'.$parts['pass'];
        }
    }

    /**
     * Create a URI string from its various parts.
     *
     * @param string $scheme
     * @param string $authority
     * @param string $path
     * @param string $query
     * @param string $fragment
     *
     * @return string
     */
    private static function createUriString($scheme, $authority, $path, $query, $fragment): string
    {
        $uri = '';

        if ($scheme != '') {
            $uri .= $scheme.':';
        }

        if ($authority != '') {
            $uri .= '//'.$authority;
        }

        if ($path != '') {
            if ($path[0] !== '/') {
                if ($authority != '') {
                    // If the path is rootless and an authority is present, the path MUST be prefixed by "/"
                    $path = '/'.$path;
                }
            } elseif (isset($path[1]) && $path[1] === '/') {
                if ($authority == '') {
                    // If the path is starting with more than one "/" and no authority is present, the
                    // starting slashes MUST be reduced to one.
                    $path = '/'.ltrim($path, '/');
                }
            }

            $uri .= $path;
        }

        if ($query != '') {
            $uri .= '?'.$query;
        }

        if ($fragment != '') {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }

    /**
     * Is a given port non-standard for the current scheme?
     *
     * @param string $scheme
     * @param int    $port
     *
     * @return bool
     */
    private static function isNonStandardPort($scheme, $port): bool
    {
        return !isset(self::$schemes[$scheme]) || $port !== self::$schemes[$scheme];
    }

    /**
     * @param string $scheme
     *
     * @throws \InvalidArgumentException If the scheme is invalid.
     *
     * @return string
     */
    private function filterScheme($scheme): string
    {
        if (!is_string($scheme)) {
            throw new \InvalidArgumentException('Scheme must be a string');
        }

        return strtolower($scheme);
    }

    /**
     * @param string $host
     *
     * @throws \InvalidArgumentException If the host is invalid.
     *
     * @return string
     */
    private function filterHost($host): string
    {
        if (!is_string($host)) {
            throw new \InvalidArgumentException('Host must be a string');
        }

        return strtolower($host);
    }

    /**
     * @param int|null $port
     *
     * @throws \InvalidArgumentException If the port is invalid.
     *
     * @return int|null
     */
    private function filterPort($port)
    {
        if ($port === null) {
            return;
        }

        $port = (int) $port;
        if (1 > $port || 0xffff < $port) {
            throw new \InvalidArgumentException(
                sprintf('Invalid port: %d. Must be between 1 and 65535', $port)
            );
        }

        return self::isNonStandardPort($this->scheme, $port) ? $port : null;
    }

    /**
     * Filters the path of a URI.
     *
     * @param string $path
     *
     * @throws \InvalidArgumentException If the path is invalid.
     *
     * @return string
     */
    private function filterPath($path): string
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');
        }

        return preg_replace_callback(
            '/(?:[^'.self::$charUnreserved.self::$charSubDelims.'%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $path
        );
    }

    /**
     * Filters the query string or fragment of a URI.
     *
     * @param string $str
     *
     * @throws \InvalidArgumentException If the query or fragment is invalid.
     *
     * @return string
     */
    private function filterQueryAndFragment($str): string
    {
        if (!is_string($str)) {
            throw new \InvalidArgumentException('Query and fragment must be a string');
        }

        return preg_replace_callback(
            '/(?:[^'.self::$charUnreserved.self::$charSubDelims.'%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $str
        );
    }

    private function rawurlencodeMatchZero(array $match): string
    {
        return rawurlencode($match[0]);
    }
}
