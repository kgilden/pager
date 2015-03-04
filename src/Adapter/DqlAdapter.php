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
 */
final class DqlAdapter implements AdapterInterface
{
    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var integer|null
     */
    private $itemCount;

    /**
     * @param Paginator $paginator
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @see Paginator
     *
     * @param Doctrine\ORM\Query|Doctrine\ORM\QueryBuilder $query               A Doctrine ORM query or query builder.
     * @param boolean                                      $fetchJoinCollection Whether the query joins a collection (true by default).
     *
     * @return SimpleDqlAdapter
     */
    public static function fromQuery($query, $fetchJoinCollection = true)
    {
        return new static(new Paginator($query, $fetchJoinCollection));
    }

    /**
     * {@inheritDoc}
     */
    public function getItemCount()
    {
        return $this->itemCount ?: $this->itemCount = $this->paginator->count();
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($offset, $limit)
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
