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

use KG\Pager\Adapter\ArrayAdapter;
use PHPUnit\Framework\TestCase;

class ArrayAdapterTest extends TestCase
{
    public function testItemCount(): void
    {
        $adapter = new ArrayAdapter(array_fill(0, 5, null));
        $this->assertEquals(5, $adapter->getItemCount());
    }

    public function testGetItems(): void
    {
        $adapter = new ArrayAdapter(array('foo', 'bar', 'baz', 'qux'));
        $this->assertEquals(array('bar', 'baz'), $adapter->getItems(1, 2));
    }
}
