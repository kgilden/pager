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

$pager = Pager::create(2 /* items per page */);
$pages = $pager->paginate(new ArrayAdapter($list), 3 /* current page */);

count($pages); // 3 - total number of pages

$pages[1]->isFirst(); // true - $pages is a 1-indexed list of pages
$pages[3]->isLast(); // true

$current = $pages->getCurrent();
$current->getNumber(); // 3 - as specified when creating the pages
$current === $pages[3]; // true
count($current); // 1, because there's only 1 remaining element on the last page

// Only here are the elements actually fetched.
array_values($current); // "eggplant"

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
$pager = new Pager(new EquallyPaged(5 /* items per page */));

?>
```

### Callbacks

Callbacks are used to modify paged items. They're added to paged objects and
are applied whenever the items are fetched for the first time. The only
requirement is that the callback must return exactly as many items as were
passed to it.

```php
<?php

use KG\Pager\Pager;
use KG\Pager\Adapter\ArrayAdapter;

$pages = Pager::create()
    ->paginate(new ArrayAdapter(array(1, 2)))
    ->callback(function (array $items) {
        foreach ($items as $key => $item) {
            $items[$key] = $item * 2;
        }

        return $items;
    })
;

iterator_to_array($pages[1]->getIterator()); // [2, 4]

?>
```

Installation
------------

Install using [composer](composer): `composer.phar require kgilden/pager`

Testing
-------

Simply run `phpunit` in the root directory of the library for the full
test suite.

License
-------

This library is under the [MIT license](LICENSE).

composer: https://getcomposer.org/download/
license: LICENSE
