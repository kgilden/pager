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
 * Pager acts as a factory to create PageInterface objects (i.e. to split
 * results into multiple pages).
 *
 * @api
 */
interface PagerInterface
{
    /**
     * Splits the items into multiple pages. `$page` is the 1-indexed page to
     * be retrieved. `$itemsPerPage` specifies how many items should be returned
     * on a single page.
     *
     * Concrete implementors MAY use their own methods to determine `$page`
     * and `$itemsPerPage` automatically. However, the arguments explicitly
     * passed must take precedence over any implicit solutions.
     *
     * @api
     */
    public function paginate(AdapterInterface $adapter, ?int $itemsPerPage = null, ?int $page = null): PageInterface;
}
