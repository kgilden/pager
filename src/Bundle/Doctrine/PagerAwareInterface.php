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

namespace KG\Pager\Bundle\Doctrine;

use KG\Pager\PagerInterface;

interface PagerAwareInterface
{
    /**
     * Sets the given pager to this object.
     */
    public function setPager(PagerInterface $pager): void;
}
