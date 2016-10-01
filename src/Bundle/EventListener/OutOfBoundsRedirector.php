<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager\Bundle\EventListener;

use KG\Pager\Exception\OutOfBoundsException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Redirects to the nearest existing page, if the current page is out of range.
 */
final class OutOfBoundsRedirector
{
    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @return RedirectResponse|null
     *
     * @throws \LogicException If the current page is inside the page range
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof OutOfBoundsException) {
            return;
        }

        $pageNumber = $exception->getPageNumber();
        $pageCount = $exception->getPageCount();

        if ($pageCount < 1) {
            return; // No pages...so let the exception fall through.
        }

        $queryBag = clone $event->getRequest()->query;

        if ($pageNumber > $pageCount) {
            $queryBag->set($exception->getRedirectKey(), $pageCount);
        } elseif ($pageNumber < 1) {
            $queryBag->set($exception->getRedirectKey(), 1);
        } else {
            return; // Super weird, because current page is within the bounds, fall through.
        }

        if (null !== $qs = http_build_query($queryBag->all(), '', '&')) {
            $qs = '?'.$qs;
        }

        // Create identical uri except for the page key in the query string which
        // was changed by this listener.
        //
        // @see Symfony\Component\HttpFoundation\Request::getUri()
        $request = $event->getRequest();
        $uri = $request->getSchemeAndHttpHost().$request->getBaseUrl().$request->getPathInfo().$qs;

        $event->setResponse(new RedirectResponse($uri));
    }
}
