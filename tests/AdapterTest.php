<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager\Tests;

use KG\Pager\Adapter;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testCannotBeInstantiated()
    {
        $reflection = new \ReflectionClass('KG\Pager\Adapter');
        $constructor = $reflection->getConstructor();
        $this->assertFalse($constructor->isPublic());
    }

    public function testArray()
    {
        $this->assertInstanceOf('KG\Pager\Adapter\ArrayAdapter', Adapter::_array(array()));
    }

    public function testDql()
    {
        if (!class_exists('Doctrine\ORM\Query')) {
            $this->markTestSkipped('doctrine/orm must be installed to run this test');
        }

        $query = $this->getMockQuery();
        $this->assertInstanceOf('KG\Pager\Adapter\DqlAdapter', Adapter::dql($query));
    }

    public function testDqlByHand()
    {
        if (!class_exists('Doctrine\ORM\Query')) {
            $this->markTestSkipped('doctrine/orm must be installed to run this test');
        }

        $a = $this->getMockQuery();
        $b = $this->getMockQuery();

        $this->assertInstanceOf('KG\Pager\Adapter\DqlByHandAdapter', Adapter::dqlByHand($a, $b));
    }

    private function getMockQuery()
    {
        return $this
            ->getMockBuilder('\Doctrine\ORM\AbstractQuery')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
    }
}
