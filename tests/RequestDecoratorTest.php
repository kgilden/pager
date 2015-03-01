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

use KG\Pager\RequestDecorator;
use Symfony\Component\HttpFoundation\Request;

class RequestDecoratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            $this->markTestSkipped('symfony/http-foundation must be installed to run this test');
        }
    }

    public function testPagianteGetsPage()
    {
        $adapter = $this->getMockAdapter();

        $pager = $this->getMockPager();
        $pager
            ->method('paginate')
            ->with($adapter, null, null)
            ->willReturn($expected = $this->getMockPage())
        ;

        $decorated = new RequestDecorator($pager, $this->getMockRequestStack());

        $this->assertSame($expected, $decorated->paginate($adapter));
    }

    public function testPaginateInfersCurrentPageFromRequest()
    {
        $pager = $this->getMockPager();
        $pager
            ->method('paginate')
            ->with($this->anything(), 5)
        ;

        $stack = $this->getMockRequestStack();
        $stack
            ->method('getCurrentRequest')
            ->willReturn(new Request(array('page' => 5)))
        ;

        $decorated = new RequestDecorator($pager, $stack);
        $decorated->paginate($this->getMockAdapter());
    }

    public function testCustomKeyUsed()
    {
        $pager = $this->getMockPager();
        $pager
            ->method('paginate')
            ->with($this->anything(), 5)
        ;

        $stack = $this->getMockRequestStack();
        $stack
            ->method('getCurrentRequest')
            ->willReturn(new Request(array('foo' => 5)))
        ;

        $decorated = new RequestDecorator($pager, $stack, 'foo');
        $decorated->paginate($this->getMockAdapter());
    }

    public function testPassedPageOverridesInferredCurrentPage()
    {
        $pager = $this->getMockPager();
        $pager
            ->method('paginate')
            ->with($this->anything(), 3)
        ;

        $stack = $this->getMockRequestStack();
        $stack
            ->expects($this->never())
            ->method('getCurrentRequest')
        ;

        $decorated = new RequestDecorator($pager, $stack);
        $decorated->paginate($this->getMockAdapter(), 3);
    }

    private function getMockAdapter()
    {
        return $this->getMock('KG\Pager\AdapterInterface');
    }

    private function getMockPage()
    {
        return $this->getMock('KG\Pager\PageInterface');
    }

    private function getMockPager()
    {
        return $this->getMock('KG\Pager\PagerInterface');
    }

    private function getMockRequestStack()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
    }
}
