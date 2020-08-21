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
     * @api
     */
    public static function _array(array $array): AdapterInterface
    {
        return new ArrayAdapter($array);
    }

    /**
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $query
     * @param boolean                                        $fetchJoinCollection
     *
     * @api
     */
    public static function dql($query, bool $fetchJoinCollection = true): AdapterInterface
    {
        return DqlAdapter::fromQuery($query, $fetchJoinCollection);
    }

    /**
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $query
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $countQuery
     *
     * @api
     */
    public static function dqlByHand($query, $countQuery): AdapterInterface
    {
        return new DqlByHandAdapter($query, $countQuery);
    }

    /**
     * @api
     */
    public static function elastica(Search $search): AdapterInterface
    {
        return new ElasticaAdapter($search);
    }

    /**
     * @api
     */
    public static function mongo(\MongoCursor $cursor): AdapterInterface
    {
        return new MongoAdapter($cursor);
    }
}
