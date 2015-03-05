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

class BoundsCheckDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException KG\Pager\Exception\OutOfBoundsException
     * @expectedExceptionMessage The current page (69) is out of the paginated page range (42)
     */
    public function testPaginateFailsIfPageOutOfBounds()
    {
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
        return $this->getMock('KG\Pager\AdapterInterface');
    }

    private function getMockPage()
    {
        return $this->getMock('KG\Pager\PageInterface');
    }

    private function getMockPager()
    {
        return $this->getMock('KG\Pager\PagerInterface');
    }
}
