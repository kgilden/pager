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

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Decorates the pager to have Request determine the current page. Current page
 * is inferred from the GET parameter "page" by default (i.e. example.com?page=3).
 */
final class RequestDecorator implements PagerInterface
{
    /**
     * @var PagerInterface
     */
    private $pager;

    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * @var string
     */
    private $key;

    /**
     * @param PagerInterface $pager
     * @param RequestStack   $stack
     * @param string         $key
     */
    public function __construct(PagerInterface $pager, RequestStack $stack, $key = 'page')
    {
        $this->pager = $pager;
        $this->stack = $stack;
        $this->key = $key;
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(AdapterInterface $adapter, $itemsPerPage = null, $page = null)
    {
        return $this->pager->paginate($adapter, $itemsPerPage, $page ?: $this->getCurrentPage());
    }

    /**
     * @return integer|null
     */
    private function getCurrentPage()
    {
        if ($request = $this->stack->getCurrentRequest()) {
            return $request->query->getInt($this->key);
        }
    }
}
