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

final class Pager implements PagerInterface
{
    /**
     * @var integer
     */
    private $itemsPerPage;

    /**
     * @param integer $itemsPerPage
     */
    public function __construct($itemsPerPage = 25)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(AdapterInterface $adapter, $page = null)
    {
        return new Paged($adapter, $page ?: 1, $this->itemsPerPage);
    }
}
