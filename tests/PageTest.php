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
    public function testGetNumber()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 2, 4, 4);
        $this->assertEquals(2, $page->getNumber());
    }

    public function testIsFirstIfOffsetZero()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 1, 0, 5);
        $this->assertTrue($page->isFirst());
    }

    public function testIsNotFirstIfOffsetNotZero()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 3, 10, 5);
        $this->assertFalse($page->isFirst());
    }

    public function testIsLastPageIfNoRemainingItemsAfterThisPage()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(15);

        $page = new Page($adapter, $this->getMockStrategy(), 3, 10, 5);
        $this->assertTrue($page->isLast());
    }

    public function testIsNotLastIfMoreItemsAfterThisPage()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(16);

        $page = new Page($adapter, $this->getMockStrategy(), 3, 10, 5);
        $this->assertFalse($page->isLast());
    }

    /**
     * 7 items in total, given a limit of 4 items per page, there should be 4
     * items on the first page.
     */
    public function testCountEqualsLimitIfNotLastPage()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(7);

        $page = new Page($adapter, $this->getMockStrategy(), 1, 0, 4);
        $this->assertCount(4, $page);
    }

    /**
     * 7 items in total, 4 on the first page and thus 3 on the second page.
     */
    public function testCountOfLastPageLessThanLimitIfNoRemainingItems()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(7);

        $page = new Page($adapter, $this->getMockStrategy(), 2, 4, 4);
        $this->assertCount(3, $page);
    }

    public function testIsTraversable()
    {
        $this->assertInstanceOf('Traversable', new Page($this->getMockAdapter(), $this->getMockStrategy(), 3, 6, 3));
    }

    public function testGetIteratorDelegatesToAdapter()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->with(9, 3)
            ->willReturn($expected = new \ArrayIterator(array(1, 2, 3)))
        ;

        $page = new Page($adapter, $this->getMockStrategy(), 4, 9, 3);
        $this->assertSame($expected, $page->getIterator());
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

        $page = new Page($adapter, $strategy, 4, 9, 3);
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

        $page = new Page($adapter, $this->getMockStrategy(), 5, 12, 4);
        $this->assertEquals(15, $page->getItemCount());
    }

    public function testCallbacksApplied()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(3);

        $adapter = $this->getMockAdapter();
        $adapter->method('getItems')->willReturn(new \ArrayIterator(array(2, 4)));

        $page = new Page($adapter, $strategy, 5, 12, 4);
        $page = $page->callback(function (array $items) {
            return array_map(function ($item) { return $item * 2; }, $items);
        });

        $this->assertEquals(array(4, 8), iterator_to_array($page->getIterator()));
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
