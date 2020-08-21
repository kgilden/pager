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

use KG\Pager\Adapter\CallbackDecorator;
use KG\Pager\AdapterInterface;
use PHPUnit\Framework\TestCase;

class CallbackDecoratorTest extends TestCase
{
    public function testItemCountDelegatedToAdapter(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter->method('getItemCount')->willReturn(42);

        $decorator = new CallbackDecorator($adapter, function (array $items) {
            return $items;
        });

        $this->assertEquals(42, $decorator->getItemCount());
    }

    public function testCallbacksAppliedToItems(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->method('getItems')
            ->with(0, 2)
            ->willReturn(array(2, 4))
        ;

        $decorator = new CallbackDecorator($adapter, function (array $items) {
            return array_map(function ($item) { return $item * 2; }, $items);
        });

        $this->assertEquals(array(4, 8), $decorator->getItems(0, 2));
    }

    public function testMultipleCallbacksAppliedInOrder(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->method('getItems')
            ->with(0, 2)
            ->willReturn(array(2, 4))
        ;

        $addFn = function (array $items) {
            return array_map(function ($item) { return $item + 2; }, $items);
        };

        $mulFn = function (array $items) {
            return array_map(function ($item) { return $item * 2; }, $items);
        };

        $decorator = new CallbackDecorator($adapter, $addFn);
        $decorator = new CallbackDecorator($decorator, $mulFn);

        $this->assertEquals(array(8, 12), $decorator->getItems(0, 2));
    }

    public function testGetItemsFailsIfItemCountDifferentAfterCallback(): void
    {
        $this->expectException(\LogicException::class);

        $adapter = $this->createMock(AdapterInterface::class);
        $adapter
            ->method('getItems')
            ->willReturn(array(1, 2))
        ;

        $fn = function (array $items) {
            return array(1);
        };

        $decorator = new CallbackDecorator($adapter, $fn);
        $decorator->getItems(0, 2);
    }
}
