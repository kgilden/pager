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

final class Page implements PageInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var PagingStrategyInterface
     */
    private $strategy;

    /**
     * @var integer
     */
    private $number;

    /**
     * @var integer
     */
    private $offset;

    /**
     * @var integer
     */
    private $length;

    /**
     * @var integer|null
     */
    private $itemCount;

    /**
     * Page number could be calculated from offset & length, but doing it this
     * way enables having pages with different number of items.
     *
     * @param AdapterInterface        $adapter
     * @param PagingStrategyInterface $strategy
     * @param integer                 $number
     * @param integer                 $offset
     * @param integer                 $length
     */
    public function __construct(AdapterInterface $adapter, PagingStrategyInterface $strategy, $number, $offset, $length)
    {
        $this->adapter = $adapter;
        $this->strategy = $strategy;
        $this->number = $number;
        $this->offset = $offset;
        $this->length = $length;
    }

    /**
     * {@inheritDoc}
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * {@inheritDoc}
     */
    public function isFirst()
    {
        return 0 === $this->offset;
    }

    /**
     * {@inheritDoc}
     */
    public function isLast()
    {
        return ($this->offset + $this->length) >= $this->getItemCount();
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        $itemCount = $this->getItemCount() - $this->offset;

        // It's either the maximum number of allowed items for this page or
        // remaining number of items.
        return $itemCount > $this->length ? $this->length : $itemCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return $this->adapter->getItems($this->offset, $this->length);
    }

    /**
     * {@inheritDoc}
     */
    public function getPageCount()
    {
        return $this->strategy->getCount($this->adapter, $this->getNumber(), $this->count());
    }

    /**
     * {@inheritDoc}
     */
    public function getItemCount()
    {
        return $this->itemCount ?: $this->itemCount = $this->adapter->getItemCount();
    }
}
