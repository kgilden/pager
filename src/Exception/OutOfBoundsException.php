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

namespace KG\Pager\Exception;

class OutOfBoundsException extends \RuntimeException
{
    private int $pageNumber;
    private int $pageCount;
    private string $redirectKey;

    public function __construct(int $pageNumber, int $pageCount, string $redirectKey = 'page', ?string $message = null)
    {
        $this->pageNumber = $pageNumber;
        $this->pageCount = $pageCount;
        $this->redirectKey = $redirectKey;

        $message = $message ?: sprintf('The current page (%d) is out of the paginated page range (%d).', $pageNumber, $pageCount);

        parent::__construct($message);
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    public function getRedirectKey(): string
    {
        return $this->redirectKey;
    }
}
