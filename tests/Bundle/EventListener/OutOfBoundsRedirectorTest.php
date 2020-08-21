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

namespace KG\Pager\Tests\Bundle\EventListener;

use KG\Pager\Bundle\EventListener\OutOfBoundsRedirector;
use KG\Pager\Exception\OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class OutOfBoundsRedirectorTest extends TestCase
{
    public function testNotRedirectsIfInvalidException(): void
    {
        $event = $this->createMock(GetResponseForExceptionEvent::class);
        $event->method('getException')->willReturn(new \Exception());
        $event->expects($this->never())->method('setResponse');

        $redirector = new OutOfBoundsRedirector();
    }

    /**
     * @dataProvider getTestData
     */
    public function testRedirection(int $pageNumber, int $pageCount, ?int $expectedPage): void
    {
        $request = Request::create('http://example.com/?a=2&page=' . $pageNumber);

        $event = $this->createMock(GetResponseForExceptionEvent::class);
        $event->method('getRequest')->willReturn($request);
        $event->method('getException')->willReturn(new OutOfBoundsException($pageNumber, $pageCount));

        if (is_null($expectedPage)) {
            $event
                ->expects($this->never())
                ->method('setResponse')
            ;
        } else {
            $testCase = $this;

            $event
                ->expects($this->once())
                ->method('setResponse')
                ->will($this->returnCallback(function ($response) use ($testCase, $expectedPage) {
                    $testCase->assertEquals('http://example.com/?a=2&page='.$expectedPage, $response->getTargetUrl());
                }))
            ;
        }

        $redirector = new OutOfBoundsRedirector();
        $redirector->onKernelException($event);
    }

    public function getTestData(): array
    {
        return [
            [3, 2, 2], // redirect to last page, if current page higher
            [0, 2, 1], // redirect to first page, if current page is not positive
            [2, 0, null], // don't redirect, if no pages exist
            [2, 4, null], // don't redirect, if the current page is inside the page range
        ];
    }
}
