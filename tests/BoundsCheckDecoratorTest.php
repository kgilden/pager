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
use KG\Pager\BoundsCheckDecorator;
use KG\Pager\Exception\OutOfBoundsException;
use KG\Pager\PageInterface;
use KG\Pager\PagerInterface;
use PHPUnit\Framework\TestCase;

class BoundsCheckDecoratorTest extends TestCase
{
    public function testPaginateFailsIfPageOutOfBounds(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('The current page (69) is out of the paginated page range (42)');

        $page = $this->createMock(PageInterface::class);
        $page->method('isOutOfBounds')->willReturn(true);
        $page->method('getNumber')->willReturn(69);
        $page->method('getPageCount')->willReturn(42);

        $pager = $this->createMock(PagerInterface::class);
        $pager->method('paginate')->willReturn($page);

        $pager = new BoundsCheckDecorator($pager);
        $pager->paginate($this->createMock(AdapterInterface::class));
    }

    public function testPaginateReturnsPage(): void
    {
        $page = $this->createMock(PageInterface::class);
        $page->method('isOutOfBounds')->willReturn(false);

        $pager = $this->createMock(PagerInterface::class);
        $pager->method('paginate')->willReturn($page);

        $pager = new BoundsCheckDecorator($pager);
        $this->assertSame($page, $pager->paginate($this->createMock(AdapterInterface::class)));
    }
}
