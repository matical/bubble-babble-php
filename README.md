# Bubble Babble 
[![Build Status](https://img.shields.io/travis/matical/bubble-babble-php.svg?style=flat-square)](https://travis-ci.org/matical/bubble-babble-php)
[![Coveralls](https://img.shields.io/coveralls/matical/bubble-babble-php.svg?style=flat-square)](https://coveralls.io/github/matical/bubble-babble-php)
[![StyleCI](https://github.styleci.io/repos/138452518/shield?branch=master)](https://github.styleci.io/repos/138452518)

## Installation
`composer require ksmz/bubblebabble`

## Basic Usage
```php
use ksmz\BubbleBabble\Factory as BubbleBabble;

BubbleBabble::encode('Pineapple'); // 'xigak-nyryk-humil-bosek-sonax'
BubbleBabble::decode('xigak-nyryk-humil-bosek-sonax'); // 'Pineapple'

BubbleBabble::validate('xigak-nyryk-humil-bosek-sonax'); // true
```

or
```php
use ksmz\BubbleBabble\Encoder;
use ksmz\BubbleBabble\Decoder;

(new Encoder())->encode($input);
(new Decoder())->encode($input);
```
