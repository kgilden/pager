<?php

namespace KG\Pager\Tests\Exception;

use KG\Pager\Exception\InvalidPageException;

class InvalidPageExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testTestGetCurrentPage()
    {
        $e = new InvalidPageException(5, 10);
        $this->assertEquals(5, $e->getCurrentPage());
    }

    public function testGetPageCount()
    {
        $e = new InvalidPageException(5, 10);
        $this->assertEquals(10, $e->getPageCount());
    }

    public function testDefaultMessageSetIfNoMessage()
    {
        $e = new InvalidPageException(5, 10);
        $this->assertEquals('The current page (5) is out of the paginated page range (10).', $e->getMessage());
    }

    public function testCustomMessageOverridesDefault()
    {
        $e = new InvalidPageException(5, 10, 'foo');
        $this->assertEquals('foo', $e->getMessage());
    }
}
