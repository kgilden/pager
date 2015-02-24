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
 * A paging strategy determines how itmes are split on individual pages. The
 * simplest strategy would be to split them equally, but more intricate
 * strategies can be imagined.
 */
interface PagingStrategyInterface
{
    /**
     * @param AdapterInterface $adapter
     * @param integer          $page
     *
     * @return array An array containing offset & length
     */
    public function getLimit(AdapterInterface $adapter, $page);

    /**
     * @param AdapterInterface $adapter
     *
     * @return integer Total number of pages
     */
    public function getCount(AdapterInterface $adapter);
}
