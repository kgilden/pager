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

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use KG\Pager\Adapter\DqlAdapter;
use PHPUnit\Framework\TestCase;

class DqlAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(Query::class)) {
            $this->markTestSkipped('doctrine/orm must be installed to run this test');
        }
    }

    public function testgetItemCountDelegatesToCount()
    {
        $paginator = $this->createMock(Paginator::class);

        $paginator
            ->expects($this->once())
            ->method('count')
            ->willReturn(5)
        ;

        $adapter = new DqlAdapter($paginator);
        $this->assertEquals(5, $adapter->getItemCount());
    }

    public function testGetItemsDelegatesToGetIterator(): void
    {
        $query = $this->getMockQuery();

        $query
            ->expects($this->once())
            ->method('setFirstResult')
            ->with(10)
            ->will($this->returnSelf())
        ;

        $query
            ->expects($this->once())
            ->method('setMaxResults')
            ->with(2)
            ->will($this->returnSelf())
        ;

        $paginator = $this->createMock(Paginator::class);
        $paginator->method('getQuery')->willReturn($query);

        $paginator
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($expected = ['foo', 'bar']))
        ;

        $adapter = new DqlAdapter($paginator);
        $this->assertSame($expected, $adapter->getItems(10, 2));
    }

    public function testFromQuery(): void
    {
        $query = $this->getMockQuery();
        $adapter = DqlAdapter::fromQuery($query);

        $this->addToAssertionCount(1);
    }

    private function getMockQuery()
    {
        return $this
            ->getMockBuilder(AbstractQuery::class)
            ->setMethods(['setParameter', 'getResult', 'getQuery', 'setFirstResult', 'setMaxResults'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
    }
}
