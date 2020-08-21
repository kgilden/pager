<?php

declare(strict_types=1);

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
     * Returns all items of the current page.
     *
     * @api
     */
    public function getItems(): array;

    /**
     * Returns all items from all pages.
     *
     * @api
     */
    public function getItemsOfAllPages(): array;

    /**
     * Returns the 1-index number of the current page.
     *
     * @api
     */
    public function getNumber(): int;

    /**
     * Gets the next page or null, if this is the last page.
     *
     * @api
     */
    public function getNext(): ?PageInterface;

    /**
     * Gets the previous page or null, if this is the first page.
     *
     * @api
     */
    public function getPrevious(): ?PageInterface;

    /**
     * Whether this is the first page.
     *
     * @api
     */
    public function isFirst(): bool;

    /**
     * Whether this is the last page.
     *
     * @api
     */
    public function isLast(): bool;

    /**
     * Whether this page is out of the paged range.
     *
     * It means that the current page represents a page for which there are
     * actually no items.
     *
     * @api
     */
    public function isOutOfBounds(): bool;

    /**
     * The total number of pages the items were split into.
     *
     * @api
     */
    public function getPageCount(): int;

    /**
     * The total number of items accross all pages.
     *
     * @api
     */
    public function getItemCount(): int;

    /**
     * Adds a callback to the page to be applied to all found items whenever
     * an item is retrieved. The method must return a page containing the
     * callback. This MAY be the current page object, but also a completely
     * new object.
     *
     * The $callback is a callback to modify items of this page.
     *
     * @api
     */
    public function callback(callable $callback): PageInterface;
}
