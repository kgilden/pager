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
use Doctrine\ORM\QueryBuilder;
use KG\Pager\AdapterInterface;

/**
 * An adapter to page Doctrine ORM's queries. Difference from regular DqlAdapter
 * is that the count query must be hand-made. This way the count query can be
 * optimized to focus solely on getting the item count. Example:
 *
 *     $countQuery = $em->createQuery('SELECT COUNT(e.id) FROM Entity e');
 *     $limitQuery = $em->createQuery('SELECT e, f FROM Entity e JOIN e.foo f');
 *
 *     $page = $pager->paginate(new DqlByHandAdapter($limitQuery, $countQuery));
 *
 * @api
 */
final class DqlByHandAdapter implements AdapterInterface
{
    /**
     * @var Query
     */
    private $countQuery;

    /**
     * @var DqlAdapter
     */
    private $adapter;

    /**
     * @param Query|QueryBuilder $limitQuery
     * @param Query|QueryBuilder $countQuery
     * @param bool               $fetchJoinCollection Whether the query joins a collection (true by default)
     *
     * @api
     */
    public function __construct($limitQuery, $countQuery, $fetchJoinCollection = true)
    {
        if ($countQuery instanceof QueryBuilder) {
            $countQuery = $countQuery->getQuery();
        }

        $this->countQuery = $countQuery;
        $this->adapter = DqlAdapter::fromQuery($limitQuery, $fetchJoinCollection);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItemCount()
    {
        try {
            return array_sum(array_map('current', $this->countQuery->getScalarResult()));
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItems($offset, $limit)
    {
        return $this->adapter->getItems($offset, $limit);
    }
}
