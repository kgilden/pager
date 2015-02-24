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
 * Decorates adapters to apply a callback to the found items.
 */
final class CallbackDecorator implements AdapterInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var \Callable[]
     */
    private $callbacks;

    /**
     * @param AdapterInterface $adapter
     * @param \Callable        $callback
     */
    public function __construct(AdapterInterface $adapter, $callback)
    {
        // Flatten multiple callback decorators into a single one.
        if ($adapter instanceof self) {
            $callbacks = $adapter->callbacks;
            $callbacks[] = $callback;
            $adapter = $adapter->adapter;
        } else {
            $callbacks = array($callback);
        }

        $this->adapter = $adapter;
        $this->callbacks = $callbacks;
    }

    /**
     * {@inheritDoc}
     */
    public function getItemCount()
    {
        return $this->adapter->getItemCount();
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($offset, $limit)
    {
        $items = iterator_to_array($this->adapter->getItems($offset, $limit));

        foreach ($this->callbacks as $callback) {
            $items = call_user_func($callback, $items);
        }

        return new \ArrayIterator($items);
    }
}
