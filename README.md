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
$page = $pager->paginate(new ArrayAdapter($list), 2 /* items per page */, 3 /* current page */);

$page->isFirst(); // false
$page->isLast(); // true - there's a total of 3 pages
$page->getNumber(); // 3 - as specified when paging

// Only now are the elements actually fetched.
count($page->getItems()); // 1
$page->getItems(); // ["eggplant"]

?>
```

### Adapters

Adapters are used to allow paging of specific types of items. The following
are supported out of the box:

* [`ArrayAdapter`](src/Adapter/ArrayAdapter.php)
* [`DqlAdapter`](src/Adapter/DqlAdapter.php)
* [`DqlByHandAdapter`](src/Adapter/DqlByHandAdapter.php)
* [`ElasticaAdapter`](src/Adapter/ElasticaAdapter.php)

A single [`Adapter`](src/Adapter.php) can be used to construct any of the
adapters from within a single class.

### Strategies

There are several different strategies to split the results into pages. The
"equally paged" strategy is used by default.

```php
<?php

use KG\Pager\Pager;
use KG\Pager\Strategy\EquallyPaged;
use KG\Pager\Strategy\LastPageMerged;

$pagerA = new Pager(new EquallyPaged());
$pagerB = new Pager(new LastPageMerged(0.3333));

?>
```

The following strategies are supported:

- `EquallyPaged` - split items equally between the pages;
- `LastPageMerged` - split items equally between the pages, but merge the
  last two pages if there are too few items left dangling on the last page;

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

$page->getItems(); // [2, 4]

?>
```

### Avoiding expensive count queries

On bigger result sets it might be prohibitively expensive to count the total
number of items. The pager won't use adapter's count method by sticking to the
following methods:

    - Page::getNext()
    - Page::getPrevious()
    - Page::isFirst()
    - Page::isLast()
    - Page::getItems()
    - Page::getNumber()
    - Page::callback()

### Automatically detecting the current page from request

The [symfony/http-foundation](https://packagist.org/packages/symfony/http-foundation)
package is required for this feature. The pager can be wrapped by a special
decorator, which gets the current page automatically from the given request

```php
<?php

use KG\Pager\Pager;
use KG\Pager\RequestDecorator;

// Given http://example.com?page=3 & assuming there's a Symfony Request object
// in the RequestStack object.
$pager = new RequestDecorator(new Pager(), $requestStack);
$page = $pager->paginate($adapter);

$page->getNumber() // 3

?>
```

### Bounds checking

By default no bounds checking is made. You can do it yourself by calling
`Page::isOutOfBounds()` - it implies knowing the total item count, i.e. an
expensive call might be made for that piece of information. A decorator can
be used to throw exceptions for pages out of bounds.

```php
<?php

use KG\Pager\BoundsCheckDecorator;
use KG\Pager\Exception\OutOfBoundsException;
use KG\Pager\Pager;

$pager = new BoundsCheckDecorator(new Pager(), 'custom_key');

try {
    $pager->paginate($adapter, null, -5);
} catch (OutOfBoundsException $e) {
    // Location: http://example.com?custom_key=1
    header(sprintf('Location: http://example.com?%s=%s', $e->getRedirectKey(), 1));
}

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
