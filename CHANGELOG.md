Changelog
=========

### 1.2.1 (2020-02-21)

* Added Twig extension.
* Dropped support for PHP v5.3 & HHVM.

### 1.2.0 (2016-10-01)

* Merged `kgilden/pager-bundle` to this package
* Made `Page::isOutOfBounds` not cause counting rows

### 1.1.1 (2016-03-12)

* Added build matrix to test for highest and lowest dependencies
* Support for Symfony v3.x

### 1.1.0 (2015-12-22)

* Added `Page::getItemsOfAllPages()` to fetch all items from all pages

### 1.0.0 (2015-05-09)

* Minor refactorings
* New `MongoAdapter` to page mongoDB cursors

### 1.0.0-beta1 (2015-03-22)

* New generic Adapter to create any adapter
* New `ElasticaAdapter` to page ElasticSearch queries
* New `DqlByHandAdapter` to page DQL queries and provide a manually created count
  query;
* Added `getNext` and `getPrevious` methods to get next and previous pages
* Always have at least 1 page
* Made items per page configurable via `Pager::__construct`
* Added bounds checking (i.e. seeing whether a page is out of bounds)
* Fixed arguments of `Page` passed flipped when adding a callback
* `CallbackDecorator` fails, if the item count differs after the callback
* Fixed `CachedDecorator` not returning items, if they equalled `null`
* Fixed `CachedDecorator` asking for items again if none found at first
* Marked the public API with `@api` annotations

### 0.2.0 (2015-03-01)

* Added decorator to infer current page straight from Request
* Added new "last merged" paging strategy
* Adapters return arrays of items instead of iterators
* Got rid of Paged object
* Made items per page configurable via Pager::paginate

### 0.1.0 (2015-02-25)

* Initial prototype

