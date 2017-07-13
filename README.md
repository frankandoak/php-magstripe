# php-magstripe

[![Build Status](https://travis-ci.org/frankandoak/php-magstripe.svg?branch=master)](https://travis-ci.org/frankandoak/php-magstripe)

A library to read and parse data coming from magnetic card readers, following the ISO7811. Inspired
by [python-magstripe](https://github.com/davidjamesmoss/python-magstripe).

## Install

Require the package in composer.json

```
"require" : {
    "frankandoak/php-magstripe" : "1.*"
}
```

## Usage

```
<?php

        $magStripe = new MagStripe('%B4242424242424242^SURNAME/FIRSTNAME I^15052011000000000000?;4242424242424242=15052011000000000000?');
        // Then use the getters to fetch the info
        echo $magStripe->getAccount(); // 4242424242424242
        echo $magStripe->getName(); // FIRSTNAME I LASTNAME
        echo $magStripe->getExpYear(); // 15
        echo $magStripe->getExpMonth(); // 05
        // Raw tracks are available as an array too:
        $tracks = $magStripe->getTracks();
        echo $tracks[0]; // B4242424242424242^SURNAME/FIRSTNAME I^15052011000000000000
        echo $tracks[1]; // 4242424242424242=15052011000000000000
```

## Tests

Tests are run with php unit, validating some benchmark credit card numbers and invalid values. To run them, just run `make test`.

## License

[MIT](https://opensource.org/licenses/MIT)

