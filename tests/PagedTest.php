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

use KG\Pager\Paged;

class PagedTest extends \PHPUnit_Framework_TestCase
{
    public function testCountDelegatesToStrategy()
    {
        $adapter = $this->getMockAdapter();

        $strategy = $this->getMockStrategy();
        $strategy
            ->expects($this->once())
            ->method('getCount')
            ->with($this->identicalTo($adapter))
            ->willReturn(42);
        ;

        $this->assertCount(42, new Paged($adapter, $strategy, 1));
    }

    public function testZeroPageNotExists()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(2);

        $pages = new Paged($this->getMockAdapter(), $strategy, 1);
        $this->assertFalse(isset($pages[0]));
    }

    public function testPageExists()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(2);

        $pages = new Paged($this->getMockAdapter(), $strategy, 1);
        $this->assertTrue(isset($pages[2]));
    }

    public function testLargerPageNotExists()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(2);

        $pages = new Paged($this->getMockAdapter(), $strategy, 1);
        $this->assertFalse(isset($pages[3]));
    }

    public function testOffsetGetReturnsPage()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(3);
        $strategy->method('getLimit')->willReturn(array(10, 5));

        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(15);

        $pages = new Paged($adapter, $strategy, 2);
        $this->assertInstanceOf('KG\Pager\PageInterface', $page = $pages[3]);

        // Starting to kind of test the page implementation here, but it's the
        // only way of knowing that the parameters were passed correctly.
        $this->assertEquals(3, $page->getNumber());
        $this->assertCount(5, $page);

        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->with(10, 5)
        ;

        $page->getIterator();
    }

    public function testgetCurrentReturnsCurrentPage()
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(2);

        $pages = new Paged($this->getMockAdapter(), $strategy, 2);
        $this->assertEquals(2, $pages->getCurrent()->getNumber());
    }

    /**
     * @dataProvider getTestDataForInvalidPage
     *
     * @expectedException KG\Pager\Exception\InvalidPageException
     */
    public function testOffsetGetFailsIfInvalidPage($page)
    {
        $strategy = $this->getMockStrategy();
        $strategy->method('getCount')->willReturn(3);

        $pages = new Paged($this->getMockAdapter(), $strategy, 2);
        $pages[$page];
    }

    public function getTestDataForInvalidPage()
    {
        return array(
            array(0),
            array(4),
        );
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testCannotSetPages()
    {
        $pages = new Paged($this->getMockAdapter(), $this->getMockStrategy(), 1);
        $pages[0] = 'foo';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testCannotUnsetPages()
    {
        $pages = new Paged($this->getMockAdapter(), $this->getMockStrategy(), 1);
        unset($pages[0]);
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
