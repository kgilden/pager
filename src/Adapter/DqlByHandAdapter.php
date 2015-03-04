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

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use KG\Pager\AdapterInterface;

/**
 * An adapter to page Doctrine ORM's queries. Difference from regular DqlAdapter
 * is that the count query must be hand-made. This way the count query can be
 * optimized to focus solely on getting the item count.
 */
final class DqlByHandAdapter implements AdapterInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var Query
     */
    private $countQuery;

    /**
     * @param Query|QueryBuilder $query
     * @param Query|QueryBuilder $countQuery
     */
    public function __construct($query, $countQuery)
    {
        if ($query instanceof QueryBuilder) {
            $query = $query->getQuery();
        }

        if ($countQuery instanceof QueryBuilder) {
            $countQuery = $countQuery->getQuery();
        }

        $this->query = $query;
        $this->countQuery = $countQuery;
    }

    /**
     * {@inheritDoc}
     */
    public function getItemCount()
    {
        try {
            return array_sum(array_map('current', $this->countQuery->getScalarResult()));
        } catch (NoResultException $e)  {
            return 0;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($offset, $limit)
    {
        return $this
            ->query
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult($this->query->getHydrationMode())
        ;
    }
}