Pager
=====

[![Build Status](https://img.shields.io/travis/kgilden/pager/master.svg?style=flat)](https://travis-ci.org/kgilden/pager)

Pager is a library to split results to multiple pages.

Usage
-----

Two objects are necessary to split a set of items to pages: a pager and an
adapter. Pagers convert adapters into paged objects, adapters enable specific
types of items to be based (i.e. results of a DQL query).

For the sake of simplicity, the following example shows the process with arrays.

```php
<?php

use KG\Pager\Pager;
use KG\Pager\Adapter\ArrayAdapter;

$list = array('apple', 'banana', 'cucumber', 'dragonfruit', 'eggplant');

$pager = new Pager();
$page = $pager->paginate(new ArrayAdapter($list), 3 /* current page */, 2 /* items per page */);

$page->isFirst(); // false
$page->isLast(); // true - there's a total of 3 pages
$page->getNumber(); // 3 - as specified when paging

// Only now are the elements actually fetched.
count($page); // 1
iterator_to_array($page->getIterator()); // ["eggplant"]

?>
```

### Strategies

There are several different strategies to split the results into pages. The
simplest, "equally paged" strategy is used by default. However, more intricate
strategies can be implemented. For example, the last two pages could be merged,
if there are too few items on the last page.

```php
<?php

use KG\Pager\Pager;
use KG\Pager\Strategy\EquallyPaged;

// Using the default strategy
$pager = new Pager(new EquallyPaged());

?>
```

### Callbacks

Callbacks are used to modify paged items. They're added to page objects and
are applied whenever the items are fetched for the first time. The only
requirement is that the callback must return exactly as many items as were
passed to it.

```php
<?php

use KG\Pager\Pager;
use KG\Pager\Adapter\ArrayAdapter;

$pager = new Pager();
$page = $pager
    ->paginate(new ArrayAdapter(array(1, 2)))
    ->callback(function (array $items) {
        foreach ($items as $key => $item) {
            $items[$key] = $item * 2;
        }

        return $items;
    })
;

iterator_to_array($page->getIterator()); // [2, 4]

?>
```

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
