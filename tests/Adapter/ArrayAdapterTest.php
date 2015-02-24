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

use KG\Pager\Adapter\ArrayAdapter;

class ArrayAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testItemCount()
    {
        $adapter = new ArrayAdapter(array_fill(0, 5, null));
        $this->assertEquals(5, $adapter->getItemCount());
    }

    public function testGetItems()
    {
        $adapter = new ArrayAdapter(array('foo', 'bar', 'baz', 'qux'));
        $this->assertEquals(array('bar', 'baz'), $adapter->getItems(1, 2));
    }
}
