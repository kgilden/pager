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

use KG\Pager\Adapter\CachedDecorator;
use KG\Pager\PagingStrategy\EquallyPaged;

final class Pager implements PagerInterface
{
    private PagingStrategyInterface $strategy;
    private int $perPage;

    /**
     * Creates a new pager. Default strategy "equally paged" is used, if a
     * strategy is not specified.
     */
    public function __construct(?int $perPage = null, ?PagingStrategyInterface $strategy = null)
    {
        $this->perPage = $perPage ?: 25;
        $this->strategy = $strategy ?: new EquallyPaged();
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(AdapterInterface $adapter, ?int $itemsPerPage = null, ?int $page = null): PageInterface
    {
        return new Page(new CachedDecorator($adapter), $this->strategy, $itemsPerPage ?: $this->perPage, $page ?: 1);
    }
}
