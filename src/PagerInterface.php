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
 * Pager acts as a factory to create PagedInterface objects (i.e. to split
 * results into multiple pages).
 */
interface PagerInterface
{
    /**
     * @param AdapterInterface $adapter
     * @param integer|null     $page    1-indexed current page (behavior for null is implementation-specific)
     *
     * @return PagedInterface
     */
    public function paginate(AdapterInterface $adapter, $page = null);
}
