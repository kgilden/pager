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
 *
 * @api
 */
interface PageInterface
{
    /**
     * @return array The items of this page
     *
     * @api
     */
    public function getItems();

    /**
     * @return integer 1-indexed number of this page within the page collection
     *
     * @api
     */
    public function getNumber();

    /**
     * @return PageInterface|null Next page or null, if this is the last page
     *
     * @api
     */
    public function getNext();

    /**
     * @return PageInterface|null Previous page or null, if this is the first page
     *
     * @api
     */
    public function getPrevious();

    /**
     * @return boolean Whether it's the first page
     *
     * @api
     */
    public function isFirst();

    /**
     * @return boolean Whether it's the last page
     *
     * @api
     */
    public function isLast();

    /**
     * @return boolean Whether this page is out of the paged range
     *
     * @api
     */
    public function isOutOfBounds();

    /**
     * @return integer The total number of pages the set was split into
     *
     * @api
     */
    public function getPageCount();

    /**
     * @return integer The total number of items across all pages
     *
     * @api
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
     *
     * @api
     */
    public function callback($callback);
}
