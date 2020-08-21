<?php

declare(strict_types=1);

namespace KG\Pager\Tests\Adapter;

use Elastica\Query;
use Elastica\ResultSet;
use Elastica\Search;
use KG\Pager\Adapter\ElasticaAdapter;
use PHPUnit\Framework\TestCase;

class ElasticaAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(Search::class)) {
            $this->markTestSkipped('ruflin/elastica must be installed to run this test');
        }
    }

    public function testItemCountDelegatesToSearch()
    {
        $search = $this->createMock(Search::class);
        $search->method('getQuery')->willReturn($this->createMock(Query::class));

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
        $search = $this->createMock(Search::class);
        $search->method('getQuery')->willReturn($this->createMock(Query::class));

        $search
            ->method('search')
            ->willReturn($resultSet = $this->createMock(ResultSet::class))
        ;

        $resultSet
            ->expects($this->once())
            ->method('getResults')
            ->willReturn($expected = ['foo', 'bar'])
        ;

        $adapter = new ElasticaAdapter($search);

        $this->assertSame($expected, $adapter->getItems(0, 2));
    }

    public function testGetItemsSetsOffsetAndLimit()
    {
        $query = $this->createMock(Query::class);

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

        $search = $this->createConfiguredMock(Search::class, [
            'getQuery' => $query,
            'search' => $this->createConfiguredMock(ResultSet::class, [
                'getResults' => [],
            ]),
        ]);

        $adapter = new ElasticaAdapter($search);
        $adapter->getItems(15, 5);
    }
}
