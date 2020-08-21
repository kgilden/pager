<?php

declare(strict_types=1);

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
 * Decorates adapters to apply a callback to the found items.
 */
final class CallbackDecorator implements AdapterInterface
{
    private AdapterInterface $adapter;
    private array $callbacks;

    public function __construct(AdapterInterface $adapter, callable $callback)
    {
        // Flatten multiple callback decorators into a single one.
        if ($adapter instanceof self) {
            $callbacks = $adapter->callbacks;
            $callbacks[] = $callback;
            $adapter = $adapter->adapter;
        } else {
            $callbacks = [$callback];
        }

        $this->adapter = $adapter;
        $this->callbacks = $callbacks;
    }

    /**
     * {@inheritDoc}
     */
    public function getItemCount(): int
    {
        return $this->adapter->getItemCount();
    }

    /**
     * {@inheritDoc}
     */
    public function getItems(int $offset, int $limit): array
    {
        $items = $this->adapter->getItems($offset, $limit);

        $oldCount = count($items);

        foreach ($this->callbacks as $callback) {
            $items = call_user_func($callback, $items);
        }

        $newCount = count($items);

        if ($oldCount !== $newCount) {
            throw new \LogicException(sprintf('Callbacks may not change the number of items (old count: %d, new count: %d).', $oldCount, $newCount));
        }

        return $items;
    }
}
