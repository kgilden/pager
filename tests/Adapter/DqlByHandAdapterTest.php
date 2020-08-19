<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager\Adapter\Tests;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use KG\Pager\Adapter\DqlByHandAdapter;
use PHPUnit\Framework\TestCase;

class DqlByHandAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('Doctrine\ORM\Query')) {
            $this->markTestSkipped('doctrine/orm must be installed to run this test');
        }
    }

    public function testSupportsQueryBuilders()
    {
        $mainQb = $this->getMockQueryBuilder();
        $countQb = $this->getMockQueryBuilder();

        foreach (array($mainQb, $countQb) as $qb) {
            $qb
                ->expects($this->once())
                ->method('getQuery')
            ;
        }

        $adapter = new DqlByHandAdapter($mainQb, $countQb);
    }

    public function testGetItemCountReturnsNullIfNoResultFound()
    {
        $countQuery = $this->getMockQuery();
        $countQuery
            ->method('getScalarResult')
            ->will($this->returnCallback(function () {
                throw new NoResultException();
            }))
        ;

        $adapter = new DqlByHandAdapter($this->getMockQuery(), $countQuery);
        $this->assertEquals(0, $adapter->getItemCount());
    }

    public function testItemCountSumsAllRows()
    {
        $countQuery = $this->getMockQuery();
        $countQuery
            ->method('getScalarResult')
            ->willReturn(array_fill(0, 3, array(5)))
        ;

        $adapter = new DqlByHandAdapter($this->getMockQuery(), $countQuery);
        $this->assertEquals(15, $adapter->getItemCount());
    }

    public function testGetItemsDelegatesToAdapter()
    {
        $adapter = new DqlByHandAdapter($this->getMockQuery(), $this->getMockQuery());

        $class = new \ReflectionClass($adapter);
        $property = $class->getProperty('adapter');
        $property->setAccessible(true);

        $this->assertInstanceOf('KG\Pager\Adapter\DqlAdapter', $property->getValue($adapter));
        $property->setValue($adapter, $parent = $this->getMockAdapter());

        $parent
            ->expects($this->once())
            ->method('getItems')
            ->with(5, 10)
            ->willReturn($expected = array_fill(0, 10, array('id' => 5, 'foo' => 'bar')))
        ;

        $this->assertSame($expected, $adapter->getItems(5, 10));
    }

    private function getMockAdapter()
    {
        return $this->createMock('KG\Pager\AdapterInterface');
    }

    private function getMockQuery()
    {
        return $this
            ->getMockBuilder('\Doctrine\ORM\AbstractQuery')
            ->setMethods(array(
                'getHydrationMode',
                'getQuery',
                'getResult',
                'getScalarResult',
                'setFirstResult',
                'setMaxResults',
                'setParameter',
            ))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
    }

    private function getMockQueryBuilder()
    {
        return $this
            ->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
