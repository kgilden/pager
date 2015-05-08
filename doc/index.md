Documentation
-------------

### Adapters

Adapters are used to allow paging of specific types of items. The following
are supported out of the box:

* [`ArrayAdapter`](/src/Adapter/ArrayAdapter.php)
* [`DqlAdapter`](/src/Adapter/DqlAdapter.php)
* [`DqlByHandAdapter`](/src/Adapter/DqlByHandAdapter.php)
* [`ElasticaAdapter`](/src/Adapter/ElasticaAdapter.php)
* [`MongoAdapter`](/src/Adapter/MongoAdapter.php)

A single [`Adapter`](/src/Adapter.php) can be used to construct any of the
adapters from within a single class.

```php
<?php

$pager->paginate(Adapter::_array(['foo', 'bar', 'baz']));

?>

### Strategies

Strategies define the way items are split to pages. By default the "equally
paged" strategy is used.

```php
<?php

use KG\Pager\Pager;
use KG\Pager\Strategy\EquallyPaged;
use KG\Pager\Strategy\LastPageMerged;

$pagerA = new Pager(new EquallyPaged());
$pagerB = new Pager(new LastPageMerged(0.3333));

?>
```

The following strategies exist:

- [`EquallyPaged`](/src/Strategy/EquallyPaged.php) - split items equally
  between the pages;
- [`LastPageMerged`](/src/Strategy/LastPageMerged.php) - split items equally
  between the pages, but merge the last two pages if there are too few items
  left dangling on the last page;

### Callbacks

Callbacks are used to modify paged items. They're added to page objects and
are applied whenever the items are fetched for the first time. The only
requirement is that the callback must return exactly as many items as were
passed to it.

Each callback constructs a new `Page` object so you can keep multiple pages
around.

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

### Avoiding expensive counting

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

### Automatically setting the current page

The [symfony/http-foundation](https://packagist.org/packages/symfony/http-foundation)
package is required for this feature. The pager can be wrapped by a special
decorator, which gets the current page automatically from the given request.

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

Bounds checking is disabled by default. This can be checked manually any time
by calling `Page::isOutOfBounds()`. However, this requires knowing the total
count of items.

The pager can be wrapped in a [`BoundsCheckDecorator`](/src/BoundsCheckDecorator.php)
to throw exceptions for out of bounds pages.

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
