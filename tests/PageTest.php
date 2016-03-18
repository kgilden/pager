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
            ->willReturn($expected = array(1, 2, 3))
        ;

        $page = new Page($adapter, $strategy, 3, 4);
        $this->assertSame($expected, $page->getItems());
    }

    public function testItemsOfAllPagesReturnsItemsFromAllPages()
    {
        $expected = range(0, 8);

        $strategy = $this->getMockStrategy();

        $adapter = $this->getMockAdapter();
        $adapter
            ->method('getCount')
            ->will($this->returnCallback(function () use ($expected) {
                return count($expected);
            }))
        ;

        $adapter
            ->method('getItems')
            ->will($this->returnCallback(function ($offset, $limit) use ($expected) {
                return array_slice($expected, $offset, $limit);
            }))
        ;

        $page = new Page($adapter, $strategy, 2, 2);
        $this->assertEquals($expected, $page->getItemsOfAllPages());
    }

    public function testExtraItemFetched()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getLimit')->willReturn(array(9, 3));

        $adapter = $this->getMockAdapter();
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->with(9, 4)
            ->willReturn(array(1, 2, 3, 4))
        ;

        $page = new Page($adapter, $strategy, 3, 4);
        $this->assertCount(3, $page->getItems(), 'Page may not expose the extra item.');
    }

    public function testGetNumber()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 4, 2);
        $this->assertEquals(2, $page->getNumber());
    }

    public function testGetNextReturnsNextPage()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(3);

        $adapter = $this->getMockAdapter();
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->willReturn(array_fill(0, 26, null))
        ;

        $page = new Page($adapter, $strategy, 25, 2);
        $this->assertNotNull($nextPage = $page->getNext());

        $this->assertNotSame($page, $nextPage);
        $this->assertEquals(3, $nextPage->getNumber());
    }

    public function testGetNextNullIfLastPage()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(3);

        $page = new Page($this->getMockAdapter(), $strategy, 25, 3);
        $this->assertNull($page->getNext());
    }

    public function testGetPreviousReturnsPreviousPage()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 25, 2);
        $this->assertNotNull($previousPage = $page->getPrevious());

        $this->assertNotSame($page, $previousPage);
        $this->assertEquals(1, $previousPage->getNumber());
    }

    public function testGetPreviousPageReturnsNullIfFirstPage()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 25, 1);
        $this->assertNull($page->getPrevious());
    }

    public function testIsFirstIfPageNumberFirst()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 5, 1);
        $this->assertTrue($page->isFirst());
    }

    public function testIsNotFirstIfPageNumberNotFirst()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 5, 3);
        $this->assertFalse($page->isFirst());
    }

    public function testLimitAskedOnlyOnce()
    {
        $strategy = $this->getMockStrategy();
        $strategy
            ->expects($this->once())
            ->method('getLimit')
            ->willReturn(array(0, 5))
        ;

        $adapter = $this->getMockAdapter();
        $adapter->method('getItems')->willReturn(array());

        $page = new Page($adapter, $strategy, 5, 1);
        $page->getItems();
        $page->getItems();
    }

    public function testIsLastPageIfNoRemainingItemsAfterThisPage()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getLimit')->willReturn(array(10, 5));

        $adapter = $this->getMockAdapter();
        $adapter->method('getItems')->willReturn(array_fill(0, 5, null));

        $page = new Page($adapter, $strategy, 5, 3);
        $this->assertTrue($page->isLast());
    }

    public function testIsNotLastIfMoreItemsAfterThisPage()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getLimit')->willReturn(array(10, 5));

        $adapter = $this->getMockAdapter();
        $adapter->method('getItems')->willReturn(array_fill(0, 6, null));

        $page = new Page($adapter, $strategy, 5, 3);
        $this->assertFalse($page->isLast());
    }

    public function testIsOutOfBoundsIfNumberNonPositive()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 5, 0);
        $this->assertTrue($page->isOutOfBounds());
    }

    public function testIsOutOfBoundsIfNoItemsFound()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItems')->willReturn(array());

        $page = new Page($adapter, $this->getMockStrategy(), 5, 4);
        $this->assertTrue($page->isOutOfBounds());
    }

    public function testIsNotOutOfBoundsIfPositiveAndItemsFound()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItems')->willReturn(array('a', 'b', 'c'));

        $page = new Page($adapter, $this->getMockStrategy(), 5, 3);
        $this->assertFalse($page->isOutOfBounds());
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

        $page = new Page($adapter, $strategy, 3, 4);
        $this->assertEquals(5, $page->getPageCount());
    }

    public function testPageCountNeverLessThanOne()
    {
        $strategy = $this->getMockStrategy();
        $strategy
            ->method('getCount')
            ->willReturn(0)
        ;

        $page = new Page($this->getMockAdapter(), $strategy, 1, 5);
        $this->assertEquals(1, $page->getPageCount());
    }

    public function testItemCountDelegatedToAdapter()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->expects($this->once())
            ->method('getItemCount')
            ->willReturn(15)
        ;

        $page = new Page($adapter, $this->getMockStrategy(), 4, 5);
        $this->assertEquals(15, $page->getItemCount());
    }

    public function testDifferentPageAfterCallback()
    {
        $page = new Page($this->getMockAdapter(), $this->getMockStrategy(), 5, 2);
        $newPage = $page->callback(function ($items) { return $items; });

        $this->assertNotSame($page, $newPage);

        $this->assertEquals(2, $page->getNumber());
        $this->assertEquals(2, $newPage->getNumber());
    }

    public function testCallbacksApplied()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(3);

        $adapter = $this->getMockAdapter();
        $adapter->method('getItems')->willReturn(array(2, 4));

        $page = new Page($adapter, $strategy, 4, 5);
        $page = $page->callback(function (array $items) {
            return array_map(function ($item) { return $item * 2; }, $items);
        });

        $this->assertEquals(array(4, 8), $page->getItems());
    }

    /**
     * @dataProvider getMethodsNotRelyingOnItemCount
     */
    public function testItemsNotCountedForMethod($method, $arguments = array())
    {
        $strategy = $this->getMockStrategy();

        $strategy
            ->expects($this->never())
            ->method('getCount')
        ;

        $adapter = $this->getMockAdapter();

        $adapter
            ->expects($this->never())
            ->method('getItemCount');
        ;

        $adapter
            ->method('getItems')
            ->willReturn(array(2, 4))
        ;

        $page = new Page($adapter, $strategy, 4, 5);

        call_user_func_array(array($page, $method), $arguments);
    }

    public function getMethodsNotRelyingOnItemCount()
    {
        return array(
            array('getNext'),
            array('getPrevious'),
            array('isFirst'),
            array('isLast'),
            array('isOutOfBounds'),
            array('getItems'),
            array('getNumber'),
            array('callback', array(function ($items) { return $items; })),
        );
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
