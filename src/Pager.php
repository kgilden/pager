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

use KG\Pager\PagingStrategy\EquallyPaged;

final class Pager implements PagerInterface
{
    /**
     * @var PagingStrategyInterface
     */
    private $strategy;

    /**
     * @param PagingStrategyInterface $strategy
     */
    public function __construct(PagingStrategyInterface $strategy)
    {
        $this->strategy = $strategy ?: new EquallyPaged();
    }

    /**
     * Creates a new pager with the default "equally paged" strategy.
     *
     * @param integer $perPage
     *
     * @return Pager
     */
    public static function create($perPage = 25)
    {
        return new static(new EquallyPaged($perPage));
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(AdapterInterface $adapter, $page = null)
    {
        return new Paged($adapter, $this->strategy, $page ?: 1);
    }
}
