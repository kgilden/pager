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
 * Adapter for arrays and objects which implement the \ArrayAccess & \Countable
 * interfaces.
 *
 * @api
 */
final class ArrayAdapter implements AdapterInterface
{
    private array $items;

    /**
     * @api
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItemCount(): int
    {
        return count($this->items);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItems(int $offset, int $limit): array
    {
        return array_slice($this->items, $offset, $limit);
    }
}
