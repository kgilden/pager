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

use KG\Pager\Page;

class PageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItemsDelegatesToAdapter()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getLimit')->willReturn(array(9, 3));

        $adapter = $this->getMockAdapter();
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->with(9, 3)
            ->willReturn($expected = array(1, 2, 3))
        ;

        $page = new Page($adapter, $strategy, 4, 3);
        $this->assertSame($expected, $page->getItems());
    }

    public function testGetNumber()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 2, 4);
        $this->assertEquals(2, $page->getNumber());
    }

    public function testIsFirstIfOffsetZero()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getLimit')->willReturn(array(0, 5));

        $page = new Page($this->getMockAdapter(), $strategy, 1, 5);
        $this->assertTrue($page->isFirst());
    }

    public function testIsNotFirstIfOffsetNotZero()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getLimit')->willReturn(array(10, 5));

        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 3, 5);
        $this->assertFalse($page->isFirst());
    }

    public function testIsLastPageIfNoRemainingItemsAfterThisPage()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getLimit')->willReturn(array(10, 5));

        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(15);

        $page = new Page($adapter, $strategy, 3, 5);
        $this->assertTrue($page->isLast());
    }

    public function testIsNotLastIfMoreItemsAfterThisPage()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getLimit')->willReturn(array(10, 5));

        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(16);

        $page = new Page($adapter, $strategy, 3, 5);
        $this->assertFalse($page->isLast());
    }

    public function testGetPageCountDelegatedToStrategy()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(14);

        $strategy = $this->getMockStrategy();
        $strategy
            ->expects($this->once())
            ->method('getCount')
            ->with($this->identicalTo($adapter), 4, 3)
            ->willReturn(5)
        ;

        $page = new Page($adapter, $strategy, 4, 3);
        $this->assertEquals(5, $page->getPageCount());
    }

    public function testItemCountDelegatedToAdapter()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->expects($this->once())
            ->method('getItemCount')
            ->willReturn(15)
        ;

        $page = new Page($adapter, $this->getMockStrategy(), 5, 4);
        $this->assertEquals(15, $page->getItemCount());
    }

    public function testCallbacksApplied()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(3);

        $adapter = $this->getMockAdapter();
        $adapter->method('getItems')->willReturn(array(2, 4));

        $page = new Page($adapter, $strategy, 5, 4);
        $page = $page->callback(function (array $items) {
            return array_map(function ($item) { return $item * 2; }, $items);
        });

        $this->assertEquals(array(4, 8), $page->getItems());
    }

    private function getMockAdapter()
    {
        return $this->getMock('KG\Pager\AdapterInterface');
    }

    private function getMockStrategy()
    {
        return $this->getMock('KG\Pager\PagingStrategyInterface');
    }
}
