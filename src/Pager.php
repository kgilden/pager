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

use KG\Pager\Adapter\CachedDecorator;
use KG\Pager\PagingStrategy\EquallyPaged;

final class Pager implements PagerInterface
{
    /**
     * @var PagingStrategyInterface
     */
    private $strategy;

    /**
     * Creates a new pager. Default strategy "equally paged" is used, if a
     * strategy is not specified.
     *
     * @param PagingStrategyInterface|null $strategy
     */
    public function __construct(PagingStrategyInterface $strategy = null)
    {
        $this->strategy = $strategy ?: new EquallyPaged();
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(AdapterInterface $adapter, $itemsPerPage = null, $page = null)
    {
        return new Page(new CachedDecorator($adapter), $this->strategy, $itemsPerPage ?: 25, $page ?: 1);
    }
}
