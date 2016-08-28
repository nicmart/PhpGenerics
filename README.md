# Php Generics

[![Build Status](https://travis-ci.org/nicmart/PhpGenerics.svg?branch=master)](https://travis-ci.org/nicmart/PhpGenerics)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nicmart/PhpGenerics/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nicmart/PhpGenerics/?branch=master)
[![Code Climate](https://codeclimate.com/github/nicmart/PhpGenerics/badges/gpa.svg)](https://codeclimate.com/github/nicmart/PhpGenerics)

Under development.

## Install

The best way to install Php Generics is [through composer](http://getcomposer.org).

Just create a composer.json file for your project:

```JSON
{
    "require": {
        "nicmart/php-generics": "dev-master"
    }
}
```

Then you can run these two commands to install it:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install

or simply run `composer install` if you have have already [installed the composer globally](http://getcomposer.org/doc/00-intro.md#globally).

Then you can include the autoloader, and you will have access to the library classes:

```php
<?php
require 'vendor/autoload.php';
```