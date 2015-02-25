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
interface PageInterface extends \Countable, \IteratorAggregate
{
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
     * @return integer The total number of pages the set was split into
     */
    public function getPageCount();

    /**
     * @return integer The total number of items found in the set
     */
    public function getItemCount();
}
