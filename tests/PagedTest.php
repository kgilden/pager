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
    /**
     * @dataProvider getTestDataForCount
     */
    public function testCount($itemCount, $itemsPerPage, $expectedCount)
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn($itemCount);

        $pages = new Paged($adapter, 1, $itemsPerPage);
        $this->assertCount($expectedCount, $pages);
    }

    public function getTestDataForCount()
    {
        return array(
            array(2, 2, 1),
            array(0, 2, 1),
            array(3, 2, 2),
            array(4, 2, 2),
            array(5, 1, 5),
        );
    }

    public function testZeroPageNotExists()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(5);

        $pages = new Paged($adapter, 1, 10);
        $this->assertFalse(isset($pages[0]));
    }

    public function testPageExists()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(8);

        $pages = new Paged($adapter, 1, 5);
        $this->assertTrue(isset($pages[2]));
    }

    public function testLargerPageNotExists()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(10);

        $pages = new Paged($adapter, 1, 5);
        $this->assertFalse(isset($pages[3]));
    }

    public function testOffsetGetReturnsPage()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(15);

        $pages = new Paged($adapter, 2, 5);
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
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(15);

        $pages = new Paged($adapter, 2, 5);
        $page = $pages->getCurrent();

        $this->assertEquals(2, $page->getNumber());
    }

    /**
     * @dataProvider getTestDataForInvalidPage
     *
     * @expectedException KG\Pager\Exception\InvalidPageException
     */
    public function testOffsetGetFailsIfInvalidPage($page)
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(15);

        $pages = new Paged($adapter, 2, 5);
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
        $pages = new Paged($this->getMockAdapter(), 1, 3);
        $pages[0] = 'foo';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testCannotUnsetPages()
    {
        $pages = new Paged($this->getMockAdapter(), 1, 3);
        unset($pages[0]);
    }

    private function getMockAdapter()
    {
        return $this->getMock('KG\Pager\AdapterInterface');
    }
}
