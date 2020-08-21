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

namespace KG\Pager\Tests\PagingStrategy;

use KG\Pager\AdapterInterface;
use KG\Pager\PagingStrategy\LastPageMerged;
use PHPUnit\Framework\TestCase;

class LastPageMergedTest extends TestCase
{
    /**
     * @dataProvider getTestDataForLimit
     */
    public function testGetLimit(float $threshold, int $page, int $perPage, int $itemCount, array $expectedLimit): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->method('getItems')
            ->willReturn(array_fill(0, $itemCount, null))
        ;

        $strategy = new LastPageMerged($threshold);
        $this->assertEquals($expectedLimit, $strategy->getLimit($adapter, $page, $perPage));
    }

    public function getTestDataForLimit()
    {
        return [
            [0, 1, 5, 10, [0, 5]],
            [0, 1, 5, 5, [0, 5]],
            [0.1, 1, 10, 12, [0, 10]],
            [0.5, 1, 5, 7, [0, 7]],
            [0.5, 1, 5, 8, [0, 5]],
            [0.5, 1, 5, 10, [0, 5]],
            [0.5, 3, 5, 7, [10, 7]],
            [0.5, 3, 4, 6, [8, 6]],
            [1, 1, 4, 5, [0, 5]],
            [1, 1, 4, 6, [0, 4]],
            [3, 2, 7, 10, [7, 10]],
            [6, 2, 3, 5, [3, 5]],
            // @todo what about cases where the current page is non-positive?
        ];
    }

    public function testItemsForTwoPagesAskedFromAdapter(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->method('getItems')
            ->with(0, 10)
            ->willReturn(array())
        ;

        $strategy = new LastPageMerged(0.5);
        $strategy->getLimit($adapter, 1, 5);

        $this->addToAssertionCount(1);
    }

    /**
     * @dataProvider getTestDataForCount
     */
    public function testCount(float $threshold, int $perPage, int $itemCount, int $expectedCount): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->method('getItemCount')
            ->willReturn($itemCount)
        ;

        $strategy = new LastPageMerged($threshold);
        $this->assertEquals($expectedCount, $strategy->getCount($adapter, 1, $perPage));
    }

    public function getTestDataForCount(): array
    {
        return [
            [0.0, 5, 15, 3],
            [0.5, 5, 17, 3],
            [0.1, 10, 12, 2],
            [0.5, 5, 18, 4],
            [1, 4, 13, 3],
            [1, 4, 14, 4],
            [3, 6, 15, 2],
            [3, 2, 11, 5],
        ];
    }
}
