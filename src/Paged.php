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

use KG\Pager\Exception\InvalidPageException;

final class Paged implements PagedInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var StrategyInterface
     */
    private $strategy;

    /**
     * @var integer
     */
    private $page;

    /**
     * @var integer|null
     */
    private $itemCount;

    /**
     * @param AdapterInterface        $adapter
     * @param PagingStrategyInterface $strategy
     * @param integer                 $page     Current page
     */
    public function __construct(AdapterInterface $adapter, PagingStrategyInterface $strategy, $page)
    {
        $this->adapter = $adapter;
        $this->strategy = $strategy;
        $this->page = $page;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->strategy->getCount($this->adapter);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrent()
    {
        return $this->offsetGet($this->page);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return 0 < $offset && $offset <= $this->count();
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidPageException If the offset is out of range
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new InvalidPageException($offset, $this->count());
        }

        list($itemOffset, $length) = $this->strategy->getLimit($this->adapter, $offset);

        return new Page($this->adapter, $offset, $itemOffset, $length);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException("Array access of class " . get_class($this) . " is read-only!");
    }

    /**
     * {@inheritDoc}
     *
     * @throws \BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException("Array access of class " . get_class($this) . " is read-only!");
    }

    /**
     * @return integer
     */
    private function getItemCount()
    {
        return $this->itemCount ?: $this->itemCount = $this->adapter->getItemCount();
    }
}