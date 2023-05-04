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

use Exception;
use KG\Pager\Bundle\EventListener\OutOfBoundsRedirector;
use KG\Pager\Exception\OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class OutOfBoundsRedirectorTest extends TestCase
{
    public function testNotRedirectsIfInvalidException(): void
    {
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            new Exception('Everything is on fire!')
        );

        $redirector = new OutOfBoundsRedirector();
        $redirector->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    /**
     * @dataProvider getTestData
     */
    public function testRedirection(int $pageNumber, int $pageCount, ?int $expectedPage): void
    {
        $request = Request::create('http://example.com/?a=2&page=' . $pageNumber);

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new OutOfBoundsException($pageNumber, $pageCount)
        );

        $redirector = new OutOfBoundsRedirector();
        $redirector->onKernelException($event);

        if (is_null($expectedPage)) {
            $this->assertNull($event->getResponse());
        } else {
            $this->assertInstanceOf(Response::class, $response = $event->getResponse());
            $this->assertEquals('http://example.com/?a=2&page='.$expectedPage, $response->getTargetUrl());
        }
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
