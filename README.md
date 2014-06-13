# Instantiator

This library provides a way of avoiding usage of constructors when instantiating PHP classes.

[![Build Status](https://travis-ci.org/Ocramius/Instantiator.svg?branch=master)](https://travis-ci.org/Ocramius/Instantiator)
[![Code Coverage](https://scrutinizer-ci.com/g/Ocramius/Instantiator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Ocramius/Instantiator/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Ocramius/Instantiator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Ocramius/Instantiator/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7a2c1cd1-5197-4822-8a4c-5ddaca48c769/mini.png)](https://insight.sensiolabs.com/projects/7a2c1cd1-5197-4822-8a4c-5ddaca48c769)
[![Dependency Status](https://www.versioneye.com/package/php--ocramius--instantiator/badge.svg)](https://www.versioneye.com/package/php--ocramius--instantiator)
[![HHVM Status](http://hhvm.h4cc.de/badge/ocramius/instantiator.png)](http://hhvm.h4cc.de/package/ocramius/instantiator)

[![Latest Stable Version](https://poser.pugx.org/ocramius/instantiator/v/stable.png)](https://packagist.org/packages/ocramius/instantiator)
[![Latest Unstable Version](https://poser.pugx.org/ocramius/instantiator/v/unstable.png)](https://packagist.org/packages/ocramius/instantiator)

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
php composer.phar require ocramius/instantiator:1.0.*
```

## Usage

The instantiator is able to create new instances of any class without using the constructor of the class
itself:

```php
$instantiator = new \Instantiator\Instantiator();

$instance = $instantiator->instantiate('My\\ClassName\\Here');
```

## Contributing

Please read the [CONTRIBUTING.md](CONTRIBUTING.md) contents if you wish to help out!
