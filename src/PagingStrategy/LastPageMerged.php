<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace KG\Pager\PagingStrategy;

use KG\Pager\AdapterInterface;
use KG\Pager\PagingStrategyInterface;

/**
 * Last page merged is a strategy which, given a non-negative threshold, merges
 * two last pages together, if too few items are left on the last page.
 * Threshold determines the point of decision:
 *
 *     < 1 - proportional to the number of items per page (0.1 -> 10% of items on the last page)
 *    >= 1 - number of items (3 -> 3 items on the last page)
 *
 * For example, given 10 items per page with a total of 23 items with 0.4
 * threshold, the strategy would tell the pager to merge the last two pages
 * leaving a total of 2 pages with 13 items on the last page.
 */
class LastPageMerged implements PagingStrategyInterface
{
    /**
     * @var integer
     */
    private $threshold;

    /**
     * @param integer $threshold
     */
    public function __construct($threshold)
    {
        $this->threshold = $threshold;
    }

    /**
     * {@inheritDoc}
     */
    public function getLimit(AdapterInterface $adapter, $page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        $length = $perPage;

        // Blah! This is horrible that we have to fetch 2 pages worth of data
        // every time. A solution would be to have a sort of a caching adapter.
        $itemCount = count($adapter->getItems($offset, $length * 2));

        if ($this->shouldMerge($itemCount, $perPage)) {
            $length = $itemCount;
        }

        return array($offset, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function getCount(AdapterInterface $adapter, $page, $perPage)
    {
        $itemCount = $adapter->getItemCount();
        $pageCount = (int) ceil($itemCount / $perPage);

        if ($this->shouldMerge($itemCount, $perPage)) {
            return $pageCount - 1;
        }

        return $pageCount;
    }

    private function shouldMerge($itemCount, $perPage)
    {
        $lastPageItemCount = $itemCount % $perPage;

        if ($lastPageItemCount <= 0) {
            return false;
        }

        if ($this->threshold < 1 && (($lastPageItemCount / $perPage) > $this->threshold)) {
            return false;
        }

        if ($this->threshold >= 1 && ($lastPageItemCount > $this->threshold)) {
            return false;
        }

        return true;
    }
}
