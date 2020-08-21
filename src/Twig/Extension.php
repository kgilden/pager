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

namespace KG\Pager\Twig;

use KG\Pager\Adapter;
use KG\Pager\PagerInterface;

class Extension extends \Twig_Extension
{
    private PagerInterface $pager;

    public function __construct(PagerInterface $pager)
    {
        $this->pager = $pager;
    }

    public function getFunctions(): array
    {
        return array(
            new \Twig_SimpleFunction('paged', array($this, 'paged')),
        );
    }

    public function paged(array $items, int $itemsPerPage = null, int $page = null)
    {
        return $this->pager->paginate(Adapter::_array($items), $itemsPerPage, $page);
    }
}
