Pager
=====

[![Build Status](https://img.shields.io/travis/kgilden/pager/master.svg?style=flat-square)](https://travis-ci.org/kgilden/pager)
[![Quality](https://img.shields.io/scrutinizer/g/kgilden/pager.svg?style=flat-square)](https://scrutinizer-ci.com/g/kgilden/pager/)
[![Packagist](https://img.shields.io/packagist/v/kgilden/pager.svg?style=flat-square)](https://packagist.org/packages/kgilden/pager)

Pager is a library to split results to multiple pages - any way you want them!

Usage
-----

Two objects work together to split a set of items to pages: pager and adapter.
Pagers act as factories for pages. Adapters allow concrete item sets to be
paged (for example there's an adapter for Doctrine queries).

Here's an example with arrays:

```php
<?php

use KG\Pager\Pager;
use KG\Pager\Adapter\ArrayAdapter;

$list = ['apple', 'banana', 'cucumber', 'dragonfruit', 'eggplant'];
$itemsPerPage = 2;
$currentPage = 3;

$pager = new Pager();
$page = $pager->paginate(new ArrayAdapter($list), $itemsPerPage, $currentPage);

$page->isFirst(); // false
$page->isLast(); // true - there's a total of 3 pages
$page->getNumber(); // 3 - it's $currentPage

count($page->getItems()); // 1
$page->getItems(); // ["eggplant"]

?>
```

Check out [the docs](doc/index.md) for more.

Installation
------------

Install using [composer](https://getcomposer.org/download/): `composer.phar require kgilden/pager`

Testing
-------

Simply run `phpunit` in the root directory of the library for the full
test suite.

License
-------

This library is under the [MIT license](LICENSE).
