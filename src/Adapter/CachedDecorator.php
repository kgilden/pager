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
    private AdapterInterface $adapter;
    private ?int $itemCount = null;
    private array $cached = [];

    /**
     * Position of the last item in the entire paged set. The decorator expects
     * no rows after this position.
     */
    private ?int $lastItemPos = null;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritDoc}
     */
    public function getItemCount(): int
    {
        if (null === $this->itemCount) {
            $this->itemCount = $this->adapter->getItemCount();
        }

        return $this->itemCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems(int $offset, int $limit): array
    {
        list($notCachedOffset, $notCachedLimit) = $this->findNotCachedRange($offset, $limit);

        if ($notCachedLimit > 0) {
            $this->cache($notCachedOffset, $notCachedLimit);
        }

        return $this->fromCache($offset, $limit);
    }

    private function findNotCachedRange(int $offset, int $limit): array
    {
        $begin = $offset;
        $end = $offset + $limit;

        if (null !== $this->lastItemPos && $end > $this->lastItemPos) {
            $end = $this->lastItemPos;
        }

        // Start narrowing both beginning & end indices until an item at the
        // given position is not cached.
        while ($begin < $end && array_key_exists($begin, $this->cached)) {
            $begin++;
        }

        while ($begin < $end && array_key_exists($end - 1, $this->cached)) {
            $end--;
        }

        return [$begin, $end - $begin];
    }

    /**
     * Fetches the given range of items from the adapter and stores them in
     * the cache array.
     */
    private function cache(int $offset, int $limit): void
    {
        $items = $this->adapter->getItems($offset, $limit);
        $i = $offset;

        foreach ($items as $item) {
            $this->cached[$i++] = $item;
        }

        if (count($items) < $limit) {
            $this->lastItemPos = $i - 1;
        }
    }

    /**
     * Returns the range from previously cached items.
     */
    private function fromCache(int $offset, int $limit): array
    {
        $items = [];

        for ($i = $offset; $i < ($offset + $limit); $i++) {
            if (!array_key_exists($i, $this->cached)) {
                break;
            }

            $items[] = $this->cached[$i];
        }

        return $items;
    }
}
