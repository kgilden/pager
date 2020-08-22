<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager;

use Doctrine\ORM\QueryBuilder;
use Elastica\Search;
use KG\Pager\Adapter\ArrayAdapter;
use KG\Pager\Adapter\DqlAdapter;
use KG\Pager\Adapter\DqlByHandAdapter;
use KG\Pager\Adapter\ElasticaAdapter;
use KG\Pager\Adapter\MongoAdapter;

/**
 * A single class to create any adapter from this paging library.
 *
 * @api
 */
final class Adapter
{
    private function __construct()
    {
        // This class should not be instantiated.
    }

    /**
     * It's named "_array", because "array" is a reserved keyword.
     *
     * @param array $array
     *
     * @return ArrayAdapter
     *
     * @api
     */
    public static function _array(array $array)
    {
        return new ArrayAdapter($array);
    }

    /**
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $query
     * @param boolean                                        $fetchJoinCollection
     *
     * @return DqlAdapter
     *
     * @api
     */
    public static function dql($query, $fetchJoinCollection = true)
    {
        if ($query instanceof QueryBuilder) {
            @trigger_error('Using QueryBuilder with Adapter::dql() is deprecated and will be removed in 2.0. Please use Adapter::dqlByQueryBuilder() instead.', E_USER_DEPRECATED);

            return static::dqlByQueryBuilder($query);
        }

        return DqlAdapter::fromQuery($query, $fetchJoinCollection);
    }

    /**
     * @param QueryBuilder $qb
     * @param boolean      $fetchJoinCollection
     *
     * @return DqlAdapter
     *
     * @api
     */
    public static function dqlByQueryBuilder(QueryBuilder $qb,  $fetchJoinCollection = true)
    {
        return DqlAdapter::fromQueryBuilder($qb, $fetchJoinCollection);
    }

    /**
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $query
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $countQuery
     *
     * @return DqlByHandAdapter
     *
     * @api
     */
    public static function dqlByHand($query, $countQuery)
    {
        return new DqlByHandAdapter($query, $countQuery);
    }

    /**
     * @param Search $search
     *
     * @return ElasticaAdapter
     *
     * @api
     */
    public static function elastica(Search $search)
    {
        return new ElasticaAdapter($search);
    }

    /**
     * @param \MongoCursor $cursor
     *
     * @return MongoAdapter
     *
     * @api
     */
    public static function mongo(\MongoCursor $cursor)
    {
        return new MongoAdapter($cursor);
    }
}
