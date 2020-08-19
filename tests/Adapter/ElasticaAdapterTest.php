<?php

namespace KG\Pager\Tests\Adapter;

use KG\Pager\Adapter\ElasticaAdapter;
use PHPUnit\Framework\TestCase;

class ElasticaAdapterTest extends TestCase
{
    protected function setUp()
    {
        if (!class_exists('Elastica\Search')) {
            $this->markTestSkipped('ruflin/elastica must be installed to run this test');
        }
    }

    public function testItemCountDelegatesToSearch()
    {
        $search = $this->getMockSearch();
        $search->method('getQuery')->willReturn($this->getMockQuery());

        $search
            ->expects($this->once())
            ->method('count')
            ->willReturn(42)
        ;

        $adapter = new ElasticaAdapter($search);

        $this->assertEquals(42, $adapter->getItemCount());
    }

    public function testGetItemsDelegatesToSearch()
    {
        $search = $this->getMockSearch();
        $search->method('getQuery')->willReturn($this->getMockQuery());

        $search
            ->method('search')
            ->willReturn($resultSet = $this->getMockResultSet())
        ;

        $resultSet
            ->expects($this->once())
            ->method('getResults')
            ->willReturn($expected = array('foo', 'bar'))
        ;

        $adapter = new ElasticaAdapter($search);

        $this->assertSame($expected, $adapter->getItems(0, 2));
    }

    public function testGetItemsSetsOffsetAndLimit()
    {
        $query = $this->getMockQuery();

        $query
            ->expects($this->once())
            ->method('setFrom')
            ->with(15)
        ;

        $query
            ->expects($this->once())
            ->method('setSize')
            ->with(5)
        ;

        $search = $this->getMockSearch();
        $search->method('getQuery')->willReturn($query);
        $search->method('search')->willReturn($this->getMockResultSet());

        $adapter = new ElasticaAdapter($search);
        $adapter->getItems(15, 5);
    }

    public function getMockQuery()
    {
        return $this->createMock('Elastica\Query');
    }

    public function getMockResultSet()
    {
        return $this
            ->getMockBuilder('Elastica\ResultSet')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function getMockSearch()
    {
        return $this
            ->getMockBuilder('Elastica\Search')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
