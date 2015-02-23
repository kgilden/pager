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
 * Adapters are the middle-men between the pager and various data sources. They
 * do the actual fetching of items for the given interval as well as know the
 * total number of items available.
 *
 * A separate adapter should be created for example for SQL quereries,
 * ElasticSearch results etc.
 */
interface AdapterInterface
{
    /**
     * @return integer Total number of items found across all pages
     */
    public function getItemCount();

    /**
     * @param integer $offset Index of the 1-st item to be returned
     * @param integer $limit  Maximum number of items to return
     *
     * @return \Traversable A traversable object containing the items
     */
    public function getItems($offset, $limit);
}
