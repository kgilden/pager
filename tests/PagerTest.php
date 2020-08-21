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

namespace KG\Pager\Tests;

use KG\Pager\AdapterInterface;
use KG\Pager\PageInterface;
use KG\Pager\Pager;
use PHPUnit\Framework\TestCase;

class PagerTest extends TestCase
{
    public function testPaginateGetsPage(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $pager = new Pager();
        $this->assertInstanceOf(PageInterface::class, $pager->paginate($adapter));
    }

    public function testPagerGetsFirstPageByDefault(): void
    {
        $pager = new Pager();
        $page = $pager->paginate($this->createMock(AdapterInterface::class));

        $this->assertTrue($page->isFirst());
    }
}
