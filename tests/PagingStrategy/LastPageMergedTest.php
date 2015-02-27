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

use KG\Pager\PagingStrategy\LastPageMerged;

class LastPageMergedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTestDataForLimit
     */
    public function testGetLimit($threshold, $page, $perPage, $itemCount, $expectedLimit)
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->method('getItems')
            ->willReturn(array_fill(0, $itemCount, null))
        ;

        $strategy = new LastPageMerged($threshold);
        $this->assertEquals($expectedLimit, $strategy->getLimit($adapter, $page, $perPage));
    }

    public function getTestDataForLimit()
    {
        return array(
            array(0, 1, 5, 10, array(0, 5)),
            array(0, 1, 5, 5, array(0, 5)),
            array(0.1, 1, 10, 12, array(0, 10)),
            array(0.5, 1, 5, 7, array(0, 7)),
            array(0.5, 1, 5, 8, array(0, 5)),
            array(0.5, 1, 5, 10, array(0, 5)),
            array(0.5, 3, 5, 7, array(10, 7)),
            array(0.5, 3, 4, 6, array(8, 6)),
            array(1, 1, 4, 5, array(0, 5)),
            array(1, 1, 4, 6, array(0, 4)),
            array(3, 2, 7, 10, array(7, 10)),
            array(6, 2, 3, 5, array(3, 5)),
            // @todo what about cases where the current page is non-positive?
        );
    }

    public function testItemsForTwoPagesAskedFromAdapter()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->method('getItems')
            ->with(0, 10)
            ->willReturn(array())
        ;

        $strategy = new LastPageMerged(0.5);
        $strategy->getLimit($adapter, 1, 5);
    }

    /**
     * @dataProvider getTestDataForCount
     */
    public function testCount($threshold, $perPage, $itemCount, $expectedCount)
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->method('getItemCount')
            ->willReturn($itemCount)
        ;

        $strategy = new LastPageMerged($threshold);
        $this->assertEquals($expectedCount, $strategy->getCount($adapter, 1, $perPage));
    }

    public function getTestDataForCount()
    {
        return array(
            array(0.0, 5, 15, 3),
            array(0.5, 5, 17, 3),
            array(0.1, 10, 12, 2),
            array(0.5, 5, 18, 4),
            array(1, 4, 13, 3),
            array(1, 4, 14, 4),
            array(3, 6, 15, 2),
            array(3, 2, 11, 5),
        );
    }

    private function getMockAdapter()
    {
        return $this->getMock('KG\Pager\AdapterInterface');
    }
}
