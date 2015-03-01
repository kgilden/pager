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

        // Start narrowing both beginning & end indices until an item at the
        // given position is not cached.
        while ($begin < $end && isset($cached[$begin])) {
            $begin++;
        }

        while ($begin < $end && isset($cached[$end - 1])) {
            $end--;
        }

        // If non-cached items were found, go get and cache them.
        if ($begin < $end) {
            $i = $begin;
            foreach($this->adapter->getItems($begin, $end - $begin) as $item) {
                $cached[$i++] = $item;
            }
        }

        $items = array();

        for ($i = $offset; $i < ($offset + $limit); $i++) {
            if (!isset($cached[$i])) {
                break;
            }

            $items[] = $cached[$i];
        }

        return $items;
    }
}
