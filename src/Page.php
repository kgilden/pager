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
     * Items of this page + potentially 1 extra item from the next page.
     *
     * @var array
     */
    private $itemsWithOneExtra;

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
    public function __construct(AdapterInterface $adapter, PagingStrategyInterface $strategy, $perPage, $number)
    {
        $this->adapter = $adapter;
        $this->strategy = $strategy;
        $this->perPage = $perPage;
        $this->number = $number;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        return array_slice($this->getItemsWithOneExtra(), 0, $this->getLength());
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
        return count($this->getItemsWithOneExtra()) <= $this->getLength();
    }

    /**
     * {@inheritDoc}
     */
    public function isOutOfBounds()
    {
        return $this->getNumber() < 1 || $this->getNumber() > $this->getPageCount();
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

    /**
     * Gets items of this page as well as the 1-st item from the next page.
     * Doing it this way keeps us from having to run the expensive total item
     * count query in some scenarios.
     *
     * @return array
     */
    private function getItemsWithOneExtra()
    {
        if (!$this->itemsWithOneExtra) {
            $this->itemsWithOneExtra = $this
                ->adapter
                ->getItems($this->getOffset(), $this->getLength() + 1)
            ;
        }

        return $this->itemsWithOneExtra;
    }
}
