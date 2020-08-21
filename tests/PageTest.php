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

use KG\Pager\Page;
use PHPUnit\Framework\TestCase;
use KG\Pager\AdapterInterface;
use KG\Pager\PagingStrategyInterface;

class PageTest extends TestCase
{
    public function testGetItemsDelegatesToAdapter(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([9, 3]);

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($expected = [1, 2, 3])
        ;

        $page = new Page($adapter, $strategy, 3, 4);
        $this->assertSame($expected, $page->getItems());
    }

    public function testItemsOfAllPagesReturnsItemsFromAllPages(): void
    {
        $expected = range(0, 8);

        $strategy = $this->createMock(PagingStrategyInterface::class);

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->method('getItemCount')
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

    public function testExtraItemFetched(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([9, 3]);

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->with(9, 4)
            ->willReturn([1, 2, 3, 4])
        ;

        $page = new Page($adapter, $strategy, 3, 4);
        $this->assertCount(3, $page->getItems(), 'Page may not expose the extra item.');
    }

    public function testGetNumber(): void
    {
        $page = new Page($this->createMock(AdapterInterface::class), $this->createMock(PagingStrategyInterface::class), 4, 2);
        $this->assertEquals(2, $page->getNumber());
    }

    public function testGetNextReturnsNextPage(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([0, 25]); // These shouldn't matter
        $strategy->method('getCount')->willReturn(3);

        $adapter = $this->createMock(AdapterInterface::class);
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

    public function testGetNextNullIfLastPage(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([999, 999]); // These shouldn't matter
        $strategy->method('getCount')->willReturn(3);

        $page = new Page($this->createMock(AdapterInterface::class), $strategy, 25, 3);
        $this->assertNull($page->getNext());
    }

    public function testGetPreviousReturnsPreviousPage(): void
    {
        $page = new Page($this->createMock(AdapterInterface::class), $this->createMock(PagingStrategyInterface::class), 25, 2);
        $this->assertNotNull($previousPage = $page->getPrevious());

        $this->assertNotSame($page, $previousPage);
        $this->assertEquals(1, $previousPage->getNumber());
    }

    public function testGetPreviousPageReturnsNullIfFirstPage(): void
    {
        $page = new Page($this->createMock(AdapterInterface::class), $this->createMock(PagingStrategyInterface::class), 25, 1);
        $this->assertNull($page->getPrevious());
    }

    public function testIsFirstIfPageNumberFirst(): void
    {
        $page = new Page($this->createMock(AdapterInterface::class), $this->createMock(PagingStrategyInterface::class), 5, 1);
        $this->assertTrue($page->isFirst());
    }

    public function testIsNotFirstIfPageNumberNotFirst(): void
    {
        $page = new Page($this->createMock(AdapterInterface::class), $this->createMock(PagingStrategyInterface::class), 5, 3);
        $this->assertFalse($page->isFirst());
    }

    public function testLimitAskedOnlyOnce(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy
            ->expects($this->once())
            ->method('getLimit')
            ->willReturn([0, 5])
        ;

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('getItems')->willReturn([]);

        $page = new Page($adapter, $strategy, 5, 1);
        $page->getItems();
        $page->getItems();
    }

    public function testIsLastPageIfNoRemainingItemsAfterThisPage(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([10, 5]);

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('getItems')->willReturn(array_fill(0, 5, null));

        $page = new Page($adapter, $strategy, 5, 3);
        $this->assertTrue($page->isLast());
    }

    public function testIsNotLastIfMoreItemsAfterThisPage(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([10, 5]);

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('getItems')->willReturn(array_fill(0, 6, null));

        $page = new Page($adapter, $strategy, 5, 3);
        $this->assertFalse($page->isLast());
    }

    public function testIsOutOfBoundsIfNumberNonPositive(): void
    {
        $page = new Page($this->createMock(AdapterInterface::class), $this->createMock(PagingStrategyInterface::class), 5, 0);
        $this->assertTrue($page->isOutOfBounds());
    }

    public function testIsOutOfBoundsIfNoItemsFound(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([999, 999]); // These shouldn't matter

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('getItems')->willReturn([]);

        $page = new Page($adapter, $strategy, 5, 4);
        $this->assertTrue($page->isOutOfBounds());
    }

    public function testIsNotOutOfBoundsIfPositiveAndItemsFound(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([999, 999]); // These shouldn't matter

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('getItems')->willReturn(['a', 'b', 'c']);

        $page = new Page($adapter, $strategy, 5, 3);
        $this->assertFalse($page->isOutOfBounds());
    }

    public function testIsNotOutOfBoundsIfNoItemsFoundOnFirstPage(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('getItems')->willReturn([]);

        $page = new Page($adapter, $this->createMock(PagingStrategyInterface::class), 5, 1);
        $this->assertFalse($page->isOutOfBounds(), 'Being on 1-st page without results implies there are no items - means we\'re not out of bounds.');
    }

    public function testGetPageCountDelegatedToStrategy(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('getItemCount')->willReturn(14);

        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([999, 999]); // These shouldn't matter
        $strategy
            ->expects($this->once())
            ->method('getCount')
            ->with($this->identicalTo($adapter), 4, 3)
            ->willReturn(5)
        ;

        $page = new Page($adapter, $strategy, 3, 4);
        $this->assertEquals(5, $page->getPageCount());
    }

    public function testPageCountNeverLessThanOne(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([999, 999]); // These shouldn't matter
        $strategy
            ->method('getCount')
            ->willReturn(0)
        ;

        $page = new Page($this->createMock(AdapterInterface::class), $strategy, 1, 5);
        $this->assertEquals(1, $page->getPageCount());
    }

    public function testItemCountDelegatedToAdapter(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([999, 999]); // These shouldn't matter

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->expects($this->once())
            ->method('getItemCount')
            ->willReturn(15)
        ;

        $page = new Page($adapter, $strategy, 4, 5);
        $this->assertEquals(15, $page->getItemCount());
    }

    public function testDifferentPageAfterCallback(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([999, 999]); // These shouldn't matter

        $page = new Page($this->createMock(AdapterInterface::class), $strategy, 5, 2);
        $newPage = $page->callback(function ($items) { return $items; });

        $this->assertNotSame($page, $newPage);

        $this->assertEquals(2, $page->getNumber());
        $this->assertEquals(2, $newPage->getNumber());
    }

    public function testCallbacksApplied(): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([999, 999]); // These shouldn't matter.
        $strategy->method('getCount')->willReturn(3);

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('getItems')->willReturn([2, 4]);

        $page = new Page($adapter, $strategy, 4, 5);
        $page = $page->callback(function (array $items) {
            return array_map(function ($item) { return $item * 2; }, $items);
        });

        $this->assertEquals([4, 8], $page->getItems());
    }

    /**
     * @dataProvider getMethodsNotRelyingOnItemCount
     */
    public function testItemsNotCountedForMethod($method, $arguments = []): void
    {
        $strategy = $this->createMock(PagingStrategyInterface::class);
        $strategy->method('getLimit')->willReturn([999, 999]); // These shouldn't matter.

        $strategy
            ->expects($this->never())
            ->method('getCount')
        ;

        $adapter = $this->createMock(AdapterInterface::class);

        $adapter
            ->expects($this->never())
            ->method('getItemCount');
        ;

        $adapter
            ->method('getItems')
            ->willReturn([2, 4])
        ;

        $page = new Page($adapter, $strategy, 4, 5);

        call_user_func_array([$page, $method], $arguments);
    }

    public function getMethodsNotRelyingOnItemCount(): array
    {
        return [
            ['getNext'],
            ['getPrevious'],
            ['isFirst'],
            ['isLast'],
            ['isOutOfBounds'],
            ['getItems'],
            ['getNumber'],
            ['callback', array(function ($items) { return $items; })],
        ];
    }
}
