<?php

namespace KG\Pager\Tests\Adapter;

use KG\Pager\Adapter\MongoAdapter;

class MongoAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('\MongoCursor')) {
            $this->markTestSkipped('ext/mongodb must be installed to run this test');
        }
    }

    public function testGetItemCountDelegatesToCount()
    {
        $cursor = $this->getMockCursor();

        $cursor
            ->expects($this->once())
            ->method('count')
            ->willReturn(5)
        ;

        $adapter = new MongoAdapter($cursor);
        $this->assertEquals(5, $adapter->getItemCount());
    }

    public function testGetItemsDelegatesToCursor()
    {
        $cursor = $this->getMockCursor();

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

        $this->mockIterator($cursor, $expected = array('foo', 'bar'));

        $adapter = new MongoAdapter($cursor);
        $this->assertEquals($expected, $adapter->getItems(10, 2));
    }

    private function getMockCursor()
    {
        return $this
            ->getMockBuilder('\MongoCursor')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    private function mockIterator($mock, $values)
    {
        $iterator = new \ArrayIterator($values);

        $methodsToMock = array(
            'current',
            'key',
            'next',
            'rewind',
            'valid',
        );

        foreach ($methodsToMock as $methodToMock) {
            $mock
                ->method($methodToMock)
                ->will($this->returnCallback(function () use ($iterator, $methodToMock) {
                    return call_user_method_array($methodToMock, $iterator, func_get_args());
                }))
            ;
        }
    }
}
