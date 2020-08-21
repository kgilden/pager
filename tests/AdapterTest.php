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

namespace KG\Pager\Tests;

use Doctrine\ORM\AbstractQuery;
use Elastica\Query;
use Elastica\Search;
use KG\Pager\Adapter;
use KG\Pager\Adapter\ArrayAdapter;
use KG\Pager\Adapter\DqlAdapter;
use KG\Pager\Adapter\DqlByHandAdapter;
use KG\Pager\Adapter\ElasticaAdapter;
use KG\Pager\Adapter\MongoAdapter;
use MongoCursor;
use PHPUnit\Framework\TestCase;

class AdapterTest extends TestCase
{
    public function testCannotBeInstantiated(): void
    {
        $reflection = new \ReflectionClass(Adapter::class);
        $constructor = $reflection->getConstructor();
        $this->assertFalse($constructor->isPublic());
    }

    public function testArray(): void
    {
        $this->assertInstanceOf(ArrayAdapter::class, Adapter::_array(array()));
    }

    public function testDql(): void
    {
        if (!class_exists(AbstractQuery::class)) {
            $this->markTestSkipped('doctrine/orm must be installed to run this test');
        }

        $query = $this->createMock(AbstractQuery::class);
        $this->assertInstanceOf(DqlAdapter::class, Adapter::dql($query));
    }

    public function testDqlByHand(): void
    {
        if (!class_exists(AbstractQuery::class)) {
            $this->markTestSkipped('doctrine/orm must be installed to run this test');
        }

        $a = $this->createMock(AbstractQuery::class);
        $b = $this->createMock(AbstractQuery::class);

        $this->assertInstanceOf(DqlByHandAdapter::class, Adapter::dqlByHand($a, $b));
    }

    public function testElastica(): void
    {
        if (!class_exists(Search::class)) {
            $this->markTestSkipped('ruflin/elastica must be installed to run this test');
        }

        $search = $this->createConfiguredMock(Search::class, [
            'getQuery' => $this->createMock(Query::class),
        ]);

        $this->assertInstanceOf(ElasticaAdapter::class, Adapter::elastica($search));
    }

    public function testMongo(): void
    {
        if (!class_exists(MongoCursor::class)) {
            $this->markTestSkipped('ext/mongodb must be installed to run this test');
        }

        $this->assertInstanceOf(MongoAdapter::class, Adapter::mongo($this->createMock(MongoCursor::class)));
    }
}
