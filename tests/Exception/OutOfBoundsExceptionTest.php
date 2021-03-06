<?php

declare(strict_types=1);

namespace KG\Pager\Tests\Exception;

use KG\Pager\Exception\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class OutOfBoundsExceptionTest extends TestCase
{
    public function testTestGetPageNumber(): void
    {
        $e = new OutOfBoundsException(5, 10);
        $this->assertEquals(5, $e->getPageNumber());
    }

    public function testGetPageCount(): void
    {
        $e = new OutOfBoundsException(5, 10);
        $this->assertEquals(10, $e->getPageCount());
    }

    public function testDefaultMessageSetIfNoMessage(): void
    {
        $e = new OutOfBoundsException(5, 10);
        $this->assertEquals('The current page (5) is out of the paginated page range (10).', $e->getMessage());
    }

    public function testRedirectKeySetByDefault(): void
    {
        $e = new OutOfBoundsException(5, 10);
        $this->assertEquals('page', $e->getRedirectKey());
    }

    public function testCustomRedirectKeyCanBeUsed(): void
    {
        $e = new OutOfBoundsException(5, 10, 'foo');
        $this->assertEquals('foo', $e->getRedirectKey());
    }

    public function testCustomMessageOverridesDefault(): void
    {
        $e = new OutOfBoundsException(5, 10, '', 'foo');
        $this->assertEquals('foo', $e->getMessage());
    }
}
