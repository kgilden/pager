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

use KG\Pager\Adapter\CallbackDecorator;

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
     * @var integer|null
     */
    private $offset;

    /**
     * @var integer|null
     */
    private $length;

    /**
     * @var integer
     */
    private $perPage;

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
     * @param integer                 $perPage
     */
    public function __construct(AdapterInterface $adapter, PagingStrategyInterface $strategy, $number, $perPage)
    {
        $this->adapter = $adapter;
        $this->strategy = $strategy;
        $this->number = $number;
        $this->perPage = $perPage;
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
        return 0 === $this->getOffset();
    }

    /**
     * {@inheritDoc}
     */
    public function isLast()
    {
        return ($this->getOffset() + $this->getLength()) >= $this->getItemCount();
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        $itemCount = $this->getItemCount() - $this->getOffset();

        // It's either the maximum number of allowed items for this page or
        // remaining number of items.
        return $itemCount > $this->getLength() ? $this->getLength() : $itemCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return $this->adapter->getItems($this->getOffset(), $this->getLength());
    }

    /**
     * {@inheritDoc}
     */
    public function getPageCount()
    {
        return $this->strategy->getCount($this->adapter, $this->getNumber(), $this->perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function getItemCount()
    {
        return $this->itemCount ?: $this->itemCount = $this->adapter->getItemCount();
    }

    /**
     * {@inheritDoc}
     */
    public function callback($callback)
    {
        $adapter = new CallbackDecorator($this->adapter, $callback);

        return new self($adapter, $this->strategy, $this->number, $this->perPage);
    }

    /**
     * @return integer
     */
    private function getOffset()
    {
        if (!$this->offset) {
            list($this->offset, $this->length) = $this->getLimit();
        }

        return $this->offset;
    }

    /**
     * @return integer
     */
    private function getLength()
    {
        if (!$this->length) {
            list($this->offset, $this->length) = $this->getLimit();
        }

        return $this->length;
    }

    /**
     * @see PagingStrategyInterface::getLimit()
     *
     * @return integer[]
     */
    private function getLimit()
    {
        return $this->strategy->getLimit($this->adapter, $this->getNumber(), $this->perPage);
    }
}
