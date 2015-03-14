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

use Elastica\Search;
use KG\Pager\AdapterInterface;

/**
 * Enables paging of elasticsearch queries via ruflin/elastica. Example:
 *
 *     $search = new \Elastica\Search($client);
 *     $search
 *         ->addIndex('foo')
 *         ->addType('bar')
 *         ->setQuery($query)
 *     ;
 *
 *     $page = $pager->paginate(new ElasticaAdapter($search));
 */
class ElasticaAdapter implements AdapterInterface
{
    /**
     * @var Search
     */
    private $search;

    /**
     * @param Search $search
     */
    public function __construct(Search $search)
    {
        $this->search = clone $search;
        $this->search->setQuery(clone $search->getQuery());
    }

    /**
     * {@inheritDoc}
     */
    public function getItemCount()
    {
        return $this->search->count();
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($offset, $limit)
    {
        $query = $this->search->getQuery();
        $query->setFrom($offset);
        $query->setSize($limit);

        return $this->search->search()->getResults();
    }
}
