# PSR-7 implementation

[![Latest Version](https://img.shields.io/github/release/Nyholm/psr7.svg?style=flat-square)](https://github.com/Nyholm/psr7/releases)
[![Build Status](https://img.shields.io/travis/Nyholm/psr7/master.svg?style=flat-square)](https://travis-ci.org/Nyholm/psr7)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Nyholm/psr7.svg?style=flat-square)](https://scrutinizer-ci.com/g/Nyholm/psr7)
[![Quality Score](https://img.shields.io/scrutinizer/g/Nyholm/psr7.svg?style=flat-square)](https://scrutinizer-ci.com/g/Nyholm/psr7)
[![Total Downloads](https://poser.pugx.org/nyholm/psr7/downloads)](https://packagist.org/packages/nyholm/psr7)
[![Monthly Downloads](https://poser.pugx.org/nyholm/psr7/d/monthly.png)](https://packagist.org/packages/nyholm/psr7)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)


A super lightweight PSR-7 implementation. Very strict and very fast.

| Description | Guzzle | Zend | Slim | Nyholm |
| ---- | ------ | ---- | ---- | ------ |
| Lines of code | 3 000 | 3 000 | 1 700 | 1 100 |
| PHP7 | No | No | No | Yes |
| PSR-7* | 66% | 92% | 75% | 100% |
| PSR-17 | No | No | No | Yes |
| HTTPlug | No | No | No | Yes |

\* Percent of completed tests in https://github.com/php-http/psr7-integration-tests

## Installation

```bash
composer require nyholm/psr7
```

If you are using Symfony Flex then you get all message factories registered as services. 

## Usage

The PSR-7 objects do not contain any other public methods then those defined in
the [PSR-7 specification](https://www.php-fig.org/psr/psr-7/). 

### Create objects

Use the PSR-17 factory to create requests, streams, URIs etc.  

```php
$factory = new \Nyholm\Psr7\Factory\Psr17Factory();
$request = $factory->createRequest('GET', 'http://tnyholm.se');
$steam = $factory->createStream('foobar');
```

### Sending a request

With [HTTPlug](http://httplug.io/) or any other PSR-18 (HTTP client) you may send requests like: 

```bash
composer require kriswallsmith/buzz
```

```php
$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
$psr18Client = new Buzz\Client\Curl([], $psr17Factory);

$request = (new Psr17Factory())->createRequest('GET', 'http://tnyholm.se');
$response = $psr18Client->sendRequest($request);
```

### Create server requests

```bash
composer require nyholm/psr7-server
```

```php
$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

$creator = new ServerRequestCreator(
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory
);

$serverRequest = $creator->fromGlobals();
```

### Emitting a response

```bash
composer require zendframework/zend-httphandlerrunner
```

```php
$response = (new Psr17Factory())->createReponse('200', 'Hello world');
(new \Zend\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
```
