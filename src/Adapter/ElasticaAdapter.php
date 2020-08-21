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
 *
 * @api
 */
class ElasticaAdapter implements AdapterInterface
{
    private Search $search;

    /**
     * @api
     */
    public function __construct(Search $search)
    {
        $this->search = clone $search;
        $this->search->setQuery(clone $search->getQuery());
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItemCount(): int
    {
        return $this->search->count();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItems(int $offset, int $limit): array
    {
        $query = $this->search->getQuery();
        $query->setFrom($offset);
        $query->setSize($limit);

        return $this->search->search()->getResults();
    }
}
