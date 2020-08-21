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

use KG\Pager\Exception\OutOfBoundsException;

/**
 * Makes sure the page is not out of bounds. This check is made in a separate
 * decorator, because out of bounds checking implies knowing the total number
 * of items. This may be a performance penantly for some use cases.
 */
final class BoundsCheckDecorator implements PagerInterface
{
    private PagerInterface  $pager;
    private string $redirectKey;

    public function __construct(PagerInterface $pager, string $redirectKey = 'page')
    {
        $this->pager = $pager;
        $this->redirectKey = $redirectKey;
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(AdapterInterface $adapter, ?int $itemsPerPage = null, ?int $page = null): PageInterface
    {
        $page = $this->pager->paginate($adapter, $itemsPerPage, $page);

        if ($page->isOutOfBounds()) {
            throw new OutOfBoundsException($page->getNumber(), $page->getPageCount(), $this->redirectKey);
        }

        return $page;
    }
}
