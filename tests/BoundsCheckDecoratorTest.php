<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager\Tests;

use KG\Pager\BoundsCheckDecorator;
use KG\Pager\Exception\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class BoundsCheckDecoratorTest extends TestCase
{
    public function testPaginateFailsIfPageOutOfBounds()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('The current page (69) is out of the paginated page range (42)');

        $page = $this->getMockPage();
        $page->method('isOutOfBounds')->willReturn(true);
        $page->method('getNumber')->willReturn(69);
        $page->method('getPageCount')->willReturn(42);

        $pager = $this->getMockPager();
        $pager->method('paginate')->willReturn($page);

        $pager = new BoundsCheckDecorator($pager);
        $pager->paginate($this->getMockAdapter());
    }

    public function testPaginateReturnsPage()
    {
        $page = $this->getMockPage();
        $page->method('isOutOfBounds')->willReturn(false);

        $pager = $this->getMockPager();
        $pager->method('paginate')->willReturn($page);

        $pager = new BoundsCheckDecorator($pager);
        $this->assertSame($page, $pager->paginate($this->getMockAdapter()));
    }

    private function getMockAdapter()
    {
        return $this->createMock('KG\Pager\AdapterInterface');
    }

    private function getMockPage()
    {
        return $this->createMock('KG\Pager\PageInterface');
    }

    private function getMockPager()
    {
        return $this->createMock('KG\Pager\PagerInterface');
    }
}
