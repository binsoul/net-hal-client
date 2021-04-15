# net-hal-client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status](https://travis-ci.org/binsoul/net-hal-client.svg?branch=master)](https://travis-ci.org/binsoul/net-hal-client)

Hypertext Application Language (HAL) is an Internet Draft standard convention for defining hypermedia such as links to external resources within JSON or XML.

This package provides a client to work with HAL+JSON endpoints. It requires a PSR-18 compatible client to send HTTP requests.

## Install

Via composer:

``` bash
$ composer require binsoul/net-hal-client
```               

This package requires PSR-17 compatible request/URI factories and a PSR-18 compatible HTTP client.
If no factories are supplied, it uses [PHP-HTTP](https://php-http.org) discovery to find installed implementations.
 
For example if you want to use [Guzzle](http://guzzlephp.org) as HTTP client execute:

``` bash
$ composer require http-interop/http-factory-guzzle php-http/guzzle7-adapter
```

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/binsoul/net-hal-client.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/binsoul/net-hal-client.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/binsoul/net-hal-client
[link-downloads]: https://packagist.org/packages/binsoul/net-hal-client
[link-author]: https://github.com/binsoul
