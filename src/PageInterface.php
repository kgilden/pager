<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager;

/**
 * Represents a single page of results.
 */
interface PageInterface
{
    /**
     * @return array The items of this page
     */
    public function getItems();

    /**
     * @return integer 1-indexed number of this page within the page collection
     */
    public function getNumber();

    /**
     * @return boolean Whether it's the first page
     */
    public function isFirst();

    /**
     * @return boolean Whether it's the last page
     */
    public function isLast();

    /**
     * @return boolean Whether this page is out of the paged range
     */
    public function isOutOfBounds();

    /**
     * @return integer The total number of pages the set was split into
     */
    public function getPageCount();

    /**
     * @return integer The total number of items across all pages
     */
    public function getItemCount();

    /**
     * Adds a callback to the page to be applied to all found items whenever
     * an item is retrieved. The method must return a page containing the
     * callback. This MAY be the current page object, but also a completely
     * new object.
     *
     * @param \Callable $callback A callback to modify items of this page
     *
     * @return PageInterface
     */
    public function callback($callback);
}
