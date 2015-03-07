<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager\Tests\Adapter;

use KG\Pager\Adapter\CachedDecorator;

class CachedDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testItemCountFetchedOnlyOnce()
    {
        $adapter = $this->getMockAdapter();

        $adapter
            ->expects($this->once())
            ->method('getItemCount')
            ->willReturn(42)
        ;

        $decorator = new CachedDecorator($adapter);

        $this->assertEquals(42, $decorator->getItemCount());
        $this->assertEquals(42, $decorator->getItemCount());
    }

    public function testGetItemsNotCached()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->with(8, 4)
            ->willReturn($expected = range(0, 3))
        ;

        $decorator = new CachedDecorator($adapter);

        $this->assertEquals($expected, $decorator->getItems(8, 4));
    }

    public function testLessItemsFoundThanAskedFor()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->with(8, 6)
            ->willReturn($expected = range(0, 3))
        ;

        $decorator = new CachedDecorator($adapter);

        $this->assertEquals($expected, $decorator->getItems(8, 6));
    }

    /**
     * This makes sure the decorator won't go asking for the same range of
     * items again, if the first query returned nothing.
     */
    public function testNullResultsCached()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->willReturn(array())
        ;

        $decorator = new CachedDecorator($adapter);
        $decorator->getItems(4, 5);

        $this->assertEquals(array(), $decorator->getItems(4, 5));
    }

    public function testNoExtraCallsMadeIfPreviousItemWasAlreadyNotFound()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->willReturn(array())
        ;

        $decorator = new CachedDecorator($adapter);
        $decorator->getItems(4, 5);
        $decorator->getItems(9, 5);
    }

    public function testSupportsNullItems()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->method('getItems')
            ->willReturn($expected = array(null, null))
        ;

        $decorator = new CachedDecorator($adapter);
        $items = $decorator->getItems(1, 3);

        $this->assertEquals($expected, $items);
    }

    /**
     * @dataProvider getTestsForCachingSystem
     */
    public function testCachingSystem($targetLimits, $expectedLimits)
    {
        $adapter = $this->getMockAdapter();

        foreach ($expectedLimits as $i => $expectedLimit) {
            list($offset, $length) = $expectedLimit;

            $adapter
                ->expects($this->at($i))
                ->method('getItems')
                ->with($offset, $length)
                ->willReturn(range($offset, $offset + $length - 1))
            ;
        }

        $decorator = new CachedDecorator($adapter);

        foreach ($targetLimits as $i => $targetLimit) {
            list($offset, $length) = $targetLimit;

            $this->assertEquals(
                range($offset, $offset + $length - 1),
                $decorator->getItems($offset, $length),
                'Fail at #'.$i
            );
        }
    }

    /**
     * Each test has a list of limits asked from the decorator and another
     * list with the expected limits for calls made to the wrapped adapter.
     *
     * Comments above each test better illustrate the scenario:
     *
     *  - items marked by "=" are expected to be retrieved from the decorator;
     *  - items enclosed in brackets are expected to be retrieved from the adapter;
     *
     * @return array
     */
    public function getTestsForCachingSystem()
    {
        return array(
            array(
                //  06  07  08  09  10  11  12  13  - items
                //         [==============]         - 1st query
                //             ============         - 2nd query
                //         ============             - 3rd query
                //             ========             - 4th query
                array(array(8, 4), array(9, 3), array(8, 3), array(9, 2)),
                array(array(8, 4)),
            ),
            array(
                //  06  07  08  09  10  11  12  13  - items
                //         [==============]         - 1st query
                // [======]========                 - 2nd query
                //                 ========[======] - 3rd query
                array(array(8, 4), array(6, 4), array(10, 4)),
                array(array(8, 4), array(6, 2), array(12, 2)),
            ),
            array(
                //  06  07  08  09  10  11  12  13  - items
                // [======]                         - 1st query
                //                         [======] - 2nd query
                //     ====[==============]====     - 3rd query
                array(array(6, 2), array(12, 2), array(7, 6)),
                array(array(6, 2), array(12, 2), array(8, 4)),
            ),
            array(
                //  06  07  08  09  10  11  12  13  - items
                // [======]                         - 1st query
                //                         [======] - 2nd query
                //             [==]                 - 3rd query
                //     ====[==============]====     - 4th query
                array(array(6, 2), array(12, 2), array(9, 1), array(7, 6)),
                array(array(6, 2), array(12, 2), array(9, 1), array(8, 4)),
            ),
            array(
                //  06  07  08  09  10  11  12  13  - items
                //             [======]             - 1st query
                //         [==============]         - 2nd query
                array(array(9, 2), array(8, 4)),
                array(array(9, 2), array(8, 4)),
            ),
        );
    }

    private function getMockAdapter()
    {
        return $this->getMock('KG\Pager\AdapterInterface');
    }
}
