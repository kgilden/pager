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
     * @param PagingStrategyInterface|null $strategy
     */
    public function __construct(PagingStrategyInterface $strategy = null)
    {
        $this->strategy = $strategy ?: new EquallyPaged();
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(AdapterInterface $adapter, $page = null)
    {
        return new Paged($adapter, $this->strategy, $page ?: 1);
    }
}
