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
     * @var integer
     */
    private $page;

    /**
     * @var integer
     */
    private $itemsPerPage;

    /**
     * @var integer|null
     */
    private $itemCount;

    /**
     * @param AdapterInterface $adapter
     * @param integer          $page         Current page
     * @param integer          $itemsPerPage Number of items on a single page
     */
    public function __construct(AdapterInterface $adapter, $page, $itemsPerPage)
    {
        $this->adapter = $adapter;
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        $count = (int) ceil($this->getItemCount() / $this->itemsPerPage);

        if (0 === $count) {
            // $count is 0, if no elements were found. In that case the number
            // of pages is set to 1 to prevent any problems on the client side.
            // @todo is this really necessary?
            $count = 1;
        }

        return $count;
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

        $itemOffset = ($offset - 1) * $this->itemsPerPage;

        return new Page($this->adapter, $offset, $itemOffset, $this->itemsPerPage);
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
