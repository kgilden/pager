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

use KG\Pager\Pager;

class PagerTest extends \PHPUnit_Framework_TestCase
{
    public function testPaginateGetsPage()
    {
        $pager = new Pager();
        $this->assertInstanceOf('KG\Pager\PageInterface', $pager->paginate($this->getMockAdapter()));
    }

    public function testPagerGetsFirstPageByDefault()
    {
        $pager = new Pager();
        $page = $pager->paginate($this->getMockAdapter());

        $this->assertTrue($page->isFirst());
    }

    private function getMockAdapter()
    {
        return $this->getMock('KG\Pager\AdapterInterface');
    }
}
