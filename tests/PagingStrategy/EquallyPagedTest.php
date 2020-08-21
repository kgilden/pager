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
use KG\Pager\PagingStrategy\EquallyPaged;
use PHPUnit\Framework\TestCase;

class EquallyPagedTest extends TestCase
{
    /**
     * @dataProvider getTestDataForCount
     */
    public function testCount(int $itemCount, int $perPage, int $expectedCount): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('getItemCount')->willReturn($itemCount);

        $strategy = new EquallyPaged();
        $this->assertEquals($expectedCount, $strategy->getCount($adapter, 1, $perPage));
    }

    public function getTestDataForCount(): array
    {
        return [
            [2, 2, 1],
            [0, 2, 0],
            [3, 2, 2],
            [4, 2, 2],
            [5, 1, 5],
        ];
    }

    /**
     * @dataProvider getTestDataForLimit
     */
    public function testGetLimit(int $perPage, int $page, array $expectedLimit): void
    {
        $strategy = new EquallyPaged();
        $this->assertEquals($expectedLimit, $strategy->getLimit($this->createMock(AdapterInterface::class), $page, $perPage));
    }

    public function getTestDataForLimit()
    {
        return [
            [5, 1, [0, 5]],
            [3, 2, [3, 3]],
        ];
    }
}
