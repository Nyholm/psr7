<?php

declare(strict_types=1);

namespace Nyholm\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Trait implementing functionality common to requests and responses.
 *
 * @author Michael Dowling and contributors to guzzlehttp/psr7
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 *
 * @internal should not be used outside of Nyholm/Psr7 as it does not fall under our BC promise
 */
trait MessageTrait
{
    /** @var array Map of all registered headers, as original name => array of values */
    private $headers = [];

    /** @var array Map of lowercase header name => original name at registration */
    private $headerNames = [];

    /** @var string */
    private $protocol = '1.1';

    /** @var StreamInterface */
    private $stream;

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version): self
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($header): bool
    {
        return isset($this->headerNames[\strtolower($header)]);
    }

    public function getHeader($header): array
    {
        $header = \strtolower($header);
        if (!isset($this->headerNames[$header])) {
            return [];
        }

        $header = $this->headerNames[$header];

        return $this->headers[$header];
    }

    public function getHeaderLine($header): string
    {
        return \implode(', ', $this->getHeader($header));
    }

    public function withHeader($header, $value): self
    {
        $value = $this->validateAndTrimHeader($header, $value);
        $normalized = \strtolower($header);

        $new = clone $this;
        if (isset($new->headerNames[$normalized])) {
            unset($new->headers[$new->headerNames[$normalized]]);
        }
        $new->headerNames[$normalized] = $header;
        $new->headers[$header] = $value;

        return $new;
    }

    public function withAddedHeader($header, $value): self
    {
        if (!\is_string($header) || '' === $header) {
            throw new \InvalidArgumentException('Header name must be an RFC 7230 compatible string.');
        }

        $new = clone $this;
        $new->setHeaders([$header => $value]);

        return $new;
    }

    public function withoutHeader($header): self
    {
        $normalized = \strtolower($header);
        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }

        $header = $this->headerNames[$normalized];
        $new = clone $this;
        unset($new->headers[$header], $new->headerNames[$normalized]);

        return $new;
    }

    public function getBody(): StreamInterface
    {
        if (!$this->stream) {
            $this->stream = Stream::create('');
        }

        return $this->stream;
    }

    public function withBody(StreamInterface $body): self
    {
        if ($body === $this->stream) {
            return $this;
        }

        $new = clone $this;
        $new->stream = $body;

        return $new;
    }

    private function setHeaders(array $headers): void
    {
        foreach ($headers as $header => $value) {
            $value = $this->validateAndTrimHeader($header, $value);
            $normalized = \strtolower($header);
            if (isset($this->headerNames[$normalized])) {
                $header = $this->headerNames[$normalized];
                $this->headers[$header] = \array_merge($this->headers[$header], $value);
            } else {
                $this->headerNames[$normalized] = $header;
                $this->headers[$header] = $value;
            }
        }
    }

    /**
     * Make sure the header complies with RFC 7230.
     *
     * Header names must be a non-empty string consisting of token characters.
     *
     * Header values must be strings consisting of visible characters with all optional
     * leading and trailing whitespace stripped. This method will always strip such
     * optional whitespace. Note that the method does not allow folding whitespace within
     * the values as this was deprecated for almost all instances by the RFC.
     *
     * header-field = field-name ":" OWS field-value OWS
     * field-name   = 1*( "!" / "#" / "$" / "%" / "&" / "'" / "*" / "+" / "-" / "." / "^"
     *              / "_" / "`" / "|" / "~" / %x30-39 / ( %x41-5A / %x61-7A ) )
     * OWS          = *( SP / HTAB )
     * field-value  = *( ( %x21-7E / %x80-FF ) [ 1*( SP / HTAB ) ( %x21-7E / %x80-FF ) ] )
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2.4
     */
    private function validateAndTrimHeader($header, $values): array
    {
        if (!\is_string($header) || 1 !== \preg_match("@^[!#$%&'*+.^_`|~0-9A-Za-z-]+$@", $header)) {
            throw new \InvalidArgumentException('Header name must be an RFC 7230 compatible string.');
        }

        if (!\is_array($values)) {
            // This is simple, just one value.
            if ((!\is_numeric($values) && !\is_string($values)) || 1 !== \preg_match("@^[ \t\x21-\x7E\x80-\xFF]*$@", (string) $values)) {
                throw new \InvalidArgumentException('Header values must be RFC 7230 compatible strings.');
            }

            return [\trim((string) $values, " \t")];
        }

        if (empty($values)) {
            throw new \InvalidArgumentException('Header values must be a string or an array of strings, empty array given.');
        }

        // Assert Non empty array
        $returnValues = [];
        foreach ($values as $v) {
            if ((!\is_numeric($v) && !\is_string($v)) || 1 !== \preg_match("@^[ \t\x21-\x7E\x80-\xFF]*$@", (string) $v)) {
                throw new \InvalidArgumentException('Header values must be RFC 7230 compatible strings.');
            }

            $returnValues[] = \trim((string) $v, " \t");
        }

        return $returnValues;
    }
}
