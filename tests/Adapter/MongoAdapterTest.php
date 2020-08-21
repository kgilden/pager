<?php

declare(strict_types=1);

namespace KG\Pager\Tests\Adapter;

use KG\Pager\Adapter\MongoAdapter;
use PHPUnit\Framework\TestCase;
use MongoCursor;

class MongoAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(MongoCursor::class)) {
            $this->markTestSkipped('ext/mongodb must be installed to run this test');
        }
    }

    public function testGetItemCountDelegatesToCount(): void
    {
        $cursor = $this->createMock(Cursor::class);

        $cursor
            ->expects($this->once())
            ->method('count')
            ->willReturn(5)
        ;

        $adapter = new MongoAdapter($cursor);
        $this->assertEquals(5, $adapter->getItemCount());
    }

    public function testGetItemsDelegatesToCursor(): void
    {
        $cursor = $this->createMock(Cursor::class);

        $cursor
            ->expects($this->once())
            ->method('skip')
            ->with($this->equalTo(10))
        ;

        $cursor
            ->expects($this->once())
            ->method('limit')
            ->with($this->equalTo(2))
        ;

        $this->mockIterator($cursor, $expected = ['foo', 'bar']);

        $adapter = new MongoAdapter($cursor);
        $this->assertEquals($expected, $adapter->getItems(10, 2));
    }

    private function mockIterator($mock, $values): void
    {
        $iterator = new \ArrayIterator($values);

        $methodsToMock = [
            'current',
            'key',
            'next',
            'rewind',
            'valid',
        ];

        foreach ($methodsToMock as $methodToMock) {
            $mock
                ->method($methodToMock)
                ->will($this->returnCallback(function () use ($iterator, $methodToMock) {
                    return call_user_func_array([$iterator, $methodToMock], func_get_args());
                }))
            ;
        }
    }
}
