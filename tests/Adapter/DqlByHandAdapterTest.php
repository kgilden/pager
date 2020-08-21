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

namespace KG\Pager\Adapter\Tests;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use KG\Pager\Adapter\DqlAdapter;
use KG\Pager\Adapter\DqlByHandAdapter;
use KG\Pager\AdapterInterface;
use PHPUnit\Framework\TestCase;

class DqlByHandAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(AbstractQuery::class)) {
            $this->markTestSkipped('doctrine/orm must be installed to run this test');
        }
    }

    public function testSupportsQueryBuilders()
    {
        $mainQb = $this->createMock(QueryBuilder::class);
        $countQb = $this->createMock(QueryBuilder::class);

        foreach ([$mainQb, $countQb] as $qb) {
            $qb
                ->expects($this->once())
                ->method('getQuery')
                ->willReturn($this->createMock(AbstractQuery::class))
            ;
        }

        $adapter = new DqlByHandAdapter($mainQb, $countQb);
    }

    public function testGetItemCountReturnsNullIfNoResultFound(): void
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

    public function testItemCountSumsAllRows(): void
    {
        $countQuery = $this->getMockQuery();
        $countQuery
            ->method('getScalarResult')
            ->willReturn(array_fill(0, 3, [5]))
        ;

        $adapter = new DqlByHandAdapter($this->getMockQuery(), $countQuery);
        $this->assertEquals(15, $adapter->getItemCount());
    }

    public function testGetItemsDelegatesToAdapter(): void
    {
        $adapter = new DqlByHandAdapter($this->getMockQuery(), $this->getMockQuery());

        $class = new \ReflectionClass($adapter);
        $property = $class->getProperty('adapter');
        $property->setAccessible(true);

        $this->assertInstanceOf(DqlAdapter::class, $property->getValue($adapter));
        $property->setValue($adapter, $parent = $this->createMock(AdapterInterface::class));

        $parent
            ->expects($this->once())
            ->method('getItems')
            ->with(5, 10)
            ->willReturn($expected = array_fill(0, 10, ['id' => 5, 'foo' => 'bar']))
        ;

        $this->assertSame($expected, $adapter->getItems(5, 10));
    }

    private function getMockQuery()
    {
        return $this
            ->getMockBuilder(AbstractQuery::class)
            ->setMethods([
                'getHydrationMode',
                'getQuery',
                'getResult',
                'getScalarResult',
                'setFirstResult',
                'setMaxResults',
                'setParameter',
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
    }
}
