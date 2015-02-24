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
 * Represents a result split into pages (i.e. the result is "paged"). Any page
 * may be retrieved as long as such a page exists.
 */
interface PagedInterface extends \ArrayAccess, \Countable
{
    /**
     * Gets the current page (the meaning of "current" depends on implementation).
     *
     * @return PageInterface
     */
    public function getCurrent();

    /**
     * @param \Callable $callback A callback to modify items in the paged object.
     *
     * @return PagedInterface
     */
    public function callback($callback);
}
