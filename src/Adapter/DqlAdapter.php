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

use Doctrine\ORM\Tools\Pagination\Paginator;
use KG\Pager\AdapterInterface;

/**
 * Enables paging of Doctrine ORM's queries. Usage:
 *
 *     // Works with both Query & QueryBuilder instances.
 *     $pager->paginate(DqlAdapter::fromQuery($query));
 *
 *     // Or simply wrap Query & QueryBuilder instances in a Paginator object.
 *     $pager->paginate(new DqlAdapter(new Paginator($query)));
 *
 * Be careful with using this adapter though: performance is abysmal for
 * sufficiently complex queries. You might want to use DqlByHandAdapter in
 * such cases.
 *
 * @api
 */
final class DqlAdapter implements AdapterInterface
{
    private Paginator $paginator;
    private ?int $itemCount = null;

    /**
     * @api
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @see Paginator
     *
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $query               A Doctrine ORM query or query builder.
     * @param boolean                                        $fetchJoinCollection Whether the query joins a collection (true by default).
     *
     * @api
     */
    public static function fromQuery($query, bool $fetchJoinCollection = true): DqlAdapter
    {
        return new static(new Paginator($query, $fetchJoinCollection));
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItemCount(): int
    {
        if (null === $this->itemCount) {
            $this->itemCount = (int) $this->paginator->count();
        }

        return $this->itemCount;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItems(int $offset, int $limit): array
    {
        $this
            ->paginator
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return iterator_to_array($this->paginator->getIterator());
    }
}
