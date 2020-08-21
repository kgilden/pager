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

namespace KG\Pager\Tests\Adapter;

use KG\Pager\Adapter\CachedDecorator;
use KG\Pager\AdapterInterface;
use PHPUnit\Framework\TestCase;

class CachedDecoratorTest extends TestCase
{
    public function testItemCountFetchedOnlyOnce(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);

        $adapter
            ->expects($this->once())
            ->method('getItemCount')
            ->willReturn(42)
        ;

        $decorator = new CachedDecorator($adapter);

        $this->assertEquals(42, $decorator->getItemCount());
        $this->assertEquals(42, $decorator->getItemCount());
    }

    public function testGetItemsNotCached(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->with(8, 4)
            ->willReturn($expected = range(0, 3))
        ;

        $decorator = new CachedDecorator($adapter);

        $this->assertEquals($expected, $decorator->getItems(8, 4));
    }

    public function testLessItemsFoundThanAskedFor(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
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
    public function testNullResultsCached(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->willReturn(array())
        ;

        $decorator = new CachedDecorator($adapter);
        $decorator->getItems(4, 5);

        $this->assertEquals(array(), $decorator->getItems(4, 5));
    }

    public function testNoExtraCallsMadeIfPreviousItemWasAlreadyNotFound(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->expects($this->once())
            ->method('getItems')
            ->willReturn(array())
        ;

        $decorator = new CachedDecorator($adapter);
        $decorator->getItems(4, 5);
        $decorator->getItems(9, 5);
    }

    public function testSupportsNullItems(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
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
    public function testCachingSystem(array $targetLimits, array $expectedLimits): void
    {
        $adapter = $this->createMock(AdapterInterface::class);

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
     */
    public function getTestsForCachingSystem(): array
    {
        return [
            [
                //  06  07  08  09  10  11  12  13  - items
                //         [==============]         - 1st query
                //             ============         - 2nd query
                //         ============             - 3rd query
                //             ========             - 4th query
                [[8, 4], [9, 3], [8, 3], [9, 2]],
                [[8, 4]],
            ],
            [
                //  06  07  08  09  10  11  12  13  - items
                //         [==============]         - 1st query
                // [======]========                 - 2nd query
                //                 ========[======] - 3rd query
                [[8, 4], [6, 4], [10, 4]],
                [[8, 4], [6, 2], [12, 2]],
            ],
            [
                //  06  07  08  09  10  11  12  13  - items
                // [======]                         - 1st query
                //                         [======] - 2nd query
                //     ====[==============]====     - 3rd query
                [[6, 2], [12, 2], [7, 6]],
                [[6, 2], [12, 2], [8, 4]],
            ],
            [
                //  06  07  08  09  10  11  12  13  - items
                // [======]                         - 1st query
                //                         [======] - 2nd query
                //             [==]                 - 3rd query
                //     ====[==============]====     - 4th query
                [[6, 2], [12, 2], [9, 1], [7, 6]],
                [[6, 2], [12, 2], [9, 1], [8, 4]],
            ],
            [
                //  06  07  08  09  10  11  12  13  - items
                //             [======]             - 1st query
                //         [==============]         - 2nd query
                [[9, 2], [8, 4]],
                [[9, 2], [8, 4]],
            ],
        ];
    }
}
