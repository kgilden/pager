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

use KG\Pager\Adapter\CallbackDecorator;

class CallbackDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testItemCountDelegatedToAdapter()
    {
        $adapter = $this->getMockAdapter();
        $adapter->method('getItemCount')->willReturn(42);

        $decorator = new CallbackDecorator($adapter, function (array $items) {
            return $items;
        });

        $this->assertEquals(42, $decorator->getItemCount());
    }

    public function testCallbacksAppliedToItems()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->method('getItems')
            ->with(0, 2)
            ->willReturn(new \ArrayIterator(array(2, 4)))
        ;

        $decorator = new CallbackDecorator($adapter, function (array $items) {
            return array_map(function ($item) { return $item * 2; }, $items);
        });

        $this->assertEquals(array(4, 8), iterator_to_array($decorator->getItems(0, 2)));
    }

    public function testMultipleCallbacksAppliedInOrder()
    {
        $adapter = $this->getMockAdapter();
        $adapter
            ->method('getItems')
            ->with(0, 2)
            ->willReturn(new \ArrayIterator(array(2, 4)))
        ;

        $addFn = function (array $items) {
            return array_map(function ($item) { return $item + 2; }, $items);
        };

        $mulFn = function (array $items) {
            return array_map(function ($item) { return $item * 2; }, $items);
        };

        $decorator = new CallbackDecorator($adapter, $addFn);
        $decorator = new CallbackDecorator($decorator, $mulFn);

        $this->assertEquals(array(8, 12), iterator_to_array($decorator->getItems(0, 2)));
    }

    private function getMockAdapter()
    {
        return $this->getMock('KG\Pager\AdapterInterface');
    }
}
