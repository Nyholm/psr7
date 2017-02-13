# PSR-7 implementation

[![Latest Version](https://img.shields.io/github/release/Nyholm/psr7.svg?style=flat-square)](https://github.com/Nyholm/psr7/releases)
[![Build Status](https://img.shields.io/travis/Nyholm/psr7/master.svg?style=flat-square)](https://travis-ci.org/Nyholm/psr7)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Nyholm/psr7.svg?style=flat-square)](https://scrutinizer-ci.com/g/Nyholm/psr7)
[![Quality Score](https://img.shields.io/scrutinizer/g/Nyholm/psr7.svg?style=flat-square)](https://scrutinizer-ci.com/g/Nyholm/psr7)
[![Total Downloads](https://poser.pugx.org/nyholm/psr7/downloads)](https://packagist.org/packages/nyholm/psr7)
[![Monthly Downloads](https://poser.pugx.org/nyholm/psr7/d/monthly.png)](https://packagist.org/packages/nyholm/psr7)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)


A super lightweight PSR-7 Http client. Very strict and very fast.

| Description | Guzzle | Zend | Slim | Nyholm |
| ---- | ------ | ---- | ---- | ------ |
| Lines of code | 4 400 | 5 000 | 4 100 | 2 000 |
| PHP7 | No | No | No | Yes |
| PSR-7* | 94% | 96% | 92% | 100% |
| PSR-17 | No | No | No | Yes |
| HTTPlug | No | No | No | Yes |

\* See https://github.com/php-http/psr7-integration-tests