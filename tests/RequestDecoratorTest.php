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

use KG\Pager\AdapterInterface;
use KG\Pager\PageInterface;
use KG\Pager\PagerInterface;
use KG\Pager\RequestDecorator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestDecoratorTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(Request::class)) {
            $this->markTestSkipped('symfony/http-foundation must be installed to run this test');
        }
    }

    public function testPagianteGetsPage(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);

        $pager = $this->createMock(PagerInterface::class);
        $pager
            ->method('paginate')
            ->with($adapter, null, null)
            ->willReturn($expected = $this->createMock(PageInterface::class))
        ;

        $decorated = new RequestDecorator($pager, $this->createMock(RequestStack::class));

        $this->assertSame($expected, $decorated->paginate($adapter));
    }

    public function testPaginateInfersCurrentPageFromRequest(): void
    {
        $pager = $this->createMock(PagerInterface::class);
        $pager
            ->expects($this->once())
            ->method('paginate')
            ->with($this->anything(), $this->anything(), 5)
        ;

        $stack = $this->createMock(RequestStack::class);
        $stack
            ->method('getCurrentRequest')
            ->willReturn(new Request(['page' => 5]))
        ;

        $decorated = new RequestDecorator($pager, $stack);
        $decorated->paginate($this->createMock(AdapterInterface::class));
    }

    public function testCustomKeyUsed(): void
    {
        $pager = $this->createMock(PagerInterface::class);
        $pager
            ->expects($this->once())
            ->method('paginate')
            ->with($this->anything(), $this->anything(), 5)
        ;

        $stack = $this->createMock(RequestStack::class);
        $stack
            ->method('getCurrentRequest')
            ->willReturn(new Request(['foo' => 5]))
        ;

        $decorated = new RequestDecorator($pager, $stack, 'foo');
        $decorated->paginate($this->createMock(AdapterInterface::class));
    }

    public function testPassedPageOverridesInferredCurrentPage(): void
    {
        $pager = $this->createMock(PagerInterface::class);
        $pager
            ->method('paginate')
            ->with($this->anything(), $this->anything(), 3)
        ;

        $stack = $this->createMock(RequestStack::class);
        $stack
            ->expects($this->never())
            ->method('getCurrentRequest')
        ;

        $decorated = new RequestDecorator($pager, $stack);
        $decorated->paginate($this->createMock(AdapterInterface::class), null, 3);
    }
}
