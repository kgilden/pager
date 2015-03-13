<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager\Adapter;

use KG\Pager\AdapterInterface;

/**
 * Caches and attempts to minimize calls made to the wrapped adapter.
 */
final class CachedDecorator implements AdapterInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var integer|null
     */
    private $itemCount;

    /**
     * @var array
     */
    private $cached = array();

    /**
     * Position of the last item in the entire paged set. The decorator expects
     * no rows after this position.
     *
     * @var integer|null
     */
    private $lastItemPos;

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritDoc}
     */
    public function getItemCount()
    {
        if (null === $this->itemCount) {
            $this->itemCount = $this->adapter->getItemCount();
        }

        return $this->itemCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($offset, $limit)
    {
        $cached = &$this->cached;

        $begin = $offset;
        $end = $offset + $limit;

        if (null !== $this->lastItemPos && $end > $this->lastItemPos) {
            $end = $this->lastItemPos;
        }

        // Start narrowing both beginning & end indices until an item at the
        // given position is not cached.
        while ($begin < $end && array_key_exists($begin, $cached)) {
            $begin++;
        }

        while ($begin < $end && array_key_exists($end - 1, $cached)) {
            $end--;
        }

        // If non-cached items were found, go get and cache them.
        if ($begin < $end) {
            $i = $begin;

            $nonCachedOffset = $begin;
            $nonCachedLimit = $end - $begin;

            $nonCachedItems = $this->adapter->getItems($nonCachedOffset, $nonCachedLimit);

            foreach ($nonCachedItems as $item) {
                $cached[$i++] = $item;
            }

            if (count($nonCachedItems) < $nonCachedLimit) {
                $this->lastItemPos = $i - 1;
            }
        }

        $items = array();

        for ($i = $offset; $i < ($offset + $limit); $i++) {
            if (!array_key_exists($i, $cached)) {
                break;
            }

            $items[] = $cached[$i];
        }

        return $items;
    }
}
