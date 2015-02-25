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
 * Splits the paged target to pages with an equal number of items per page. The
 * last page might have less items depending on the total number of items to
 * page.
 */
final class EquallyPaged implements PagingStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function getLimit(AdapterInterface $adapter, $page, $perPage)
    {
        $offset = ($page - 1) * $perPage;
        $length = $perPage;

        return array($offset, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function getCount(AdapterInterface $adapter, $page, $perPage)
    {
        $count = (int) ceil($adapter->getItemCount() / $perPage);

        if (0 === $count) {
            // $count is 0, if no elements were found. In that case the number
            // of pages is set to 1 to prevent any problems on the client side.
            // @todo is this really necessary? Move to separate strategy?
            $count = 1;
        }

        return $count;
    }
}
