<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager\Tests\PagingStrategy;

use KG\Pager\PagingStrategy\EquallyPaged;

class EquallyPagedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTestDataForCount
     */
    public function testCount($itemCount, $perPage, $expectedCount)
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn($itemCount);

        $strategy = new EquallyPaged();
        $this->assertEquals($expectedCount, $strategy->getCount($adapter, 1, $perPage));
    }

    public function getTestDataForCount()
    {
        return array(
            array(2, 2, 1),
            array(0, 2, 0),
            array(3, 2, 2),
            array(4, 2, 2),
            array(5, 1, 5),
        );
    }

    /**
     * @dataProvider getTestDataForLimit
     */
    public function testGetLimit($perPage, $page, $expectedLimit)
    {
        $strategy = new EquallyPaged();
        $this->assertEquals($expectedLimit, $strategy->getLimit($this->getMockAdapter(), $page, $perPage));
    }

    public function getTestDataForLimit()
    {
        return array(
            array(5, 1, array(0, 5)),
            array(3, 2, array(3, 3)),
        );
    }

    private function getMockAdapter()
    {
        return $this->getMock('KG\Pager\AdapterInterface');
    }
}
