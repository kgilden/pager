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

namespace KG\Pager;

use KG\Pager\Adapter\CallbackDecorator;

final class Page implements PageInterface
{
    private AdapterInterface $adapter;
    private PagingStrategyInterface $strategy;
    private ?array $itemsWithOneExtra = null; // Items of this page + potentially 1 extra item from the next page.
    private int $number;
    private ?int $offset = null;
    private ?int $length = null;
    private int $perPage;
    private ?int $itemCount = null;

    /**
     * Page number could be calculated from offset & length, but doing it this
     * way enables having pages with different number of items.
     *
     * @param AdapterInterface        $adapter
     * @param PagingStrategyInterface $strategy
     * @param integer                 $perPage
     * @param integer                 $number
     */
    public function __construct(AdapterInterface $adapter, PagingStrategyInterface $strategy, int $perPage, int $number)
    {
        $this->adapter = $adapter;
        $this->strategy = $strategy;
        $this->perPage = $perPage;
        $this->number = $number;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems(): array
    {
        return array_slice($this->getItemsWithOneExtra(), 0, $this->getLength());
    }

    /**
     * {@inheritDoc}
     */
    public function getItemsOfAllPages(): array
    {
        // @todo this is suboptimal - ideally we don't need to know item count
        // to get all items. Instead the entire collection should be returned.
        // Doing it this way to minimize BC breaks.
        return $this->adapter->getItems(0, $this->getItemCount());
    }

    /**
     * {@inheritDoc}
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * {@inheritDoc}
     */
    public function getNext(): ?PageInterface
    {
        if ($this->isLast()) {
            return null;
        }

        return $this->getPage($this->number + 1);
    }

    /**
     * {@inheritDoc}
     */
    public function getPrevious(): ?PageInterface
    {
        if ($this->isFirst()) {
            return null;
        }

        return $this->getPage($this->number - 1);
    }

    /**
     * {@inheritDoc}
     */
    public function isFirst(): bool
    {
        return 1 === $this->getNumber();
    }

    /**
     * {@inheritDoc}
     */
    public function isLast(): bool
    {
        return count($this->getItemsWithOneExtra()) <= $this->getLength();
    }

    /**
     * {@inheritDoc}
     */
    public function isOutOfBounds(): bool
    {
        $number = $this->getNumber();

        if ($this->getNumber() < 1) {
            return true;
        }

        if (($number > 1) && !count($this->getItemsWithOneExtra())) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getPageCount(): int
    {
        // Should never allow a scenario with no pages even though this is
        // technically correct. So if no elements were found, the page count
        // will still be 1.
        return $this->strategy->getCount($this->adapter, $this->getNumber(), $this->perPage) ?: 1;
    }

    /**
     * {@inheritDoc}
     */
    public function getItemCount(): int
    {
        return $this->itemCount ?: $this->itemCount = $this->adapter->getItemCount();
    }

    /**
     * {@inheritDoc}
     */
    public function callback(callable $callback): PageInterface
    {
        $adapter = new CallbackDecorator($this->adapter, $callback);

        return new self($adapter, $this->strategy, $this->perPage, $this->number);
    }

    private function getOffset(): int
    {
        if (null === $this->offset) {
            list($this->offset, $this->length) = $this->getLimit();
        }

        return $this->offset;
    }

    private function getLength(): int
    {
        if (null === $this->length) {
            list($this->offset, $this->length) = $this->getLimit();
        }

        return $this->length;
    }

    /**
     * @see PagingStrategyInterface::getLimit()
     *
     * @return integer[]
     */
    private function getLimit(): array
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
    private function getItemsWithOneExtra(): array
    {
        if (null === $this->itemsWithOneExtra) {
            $this->itemsWithOneExtra = $this
                ->adapter
                ->getItems($this->getOffset(), $this->getLength() + 1)
            ;
        }

        return $this->itemsWithOneExtra ?: array();
    }

    /**
     * Creates a new page with the given number.
     */
    private function getPage(int $number): Page
    {
        return new self($this->adapter, $this->strategy, $this->perPage, $number);
    }
}
