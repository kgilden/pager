<?php

namespace KG\Pager\Tests\Twig;

use KG\Pager\Pager;
use KG\Pager\Twig\Extension;
use PHPUnit\Framework\TestCase;
use Twig\Loader\ArrayLoader;

class ExtensionTest extends TestCase
{
    public function testPageReturned()
    {
        $extension = new Extension(new Pager());

        $page = $extension->paged(['a', 'b', 'c']);

        $this->assertInstanceOf('KG\Pager\PageInterface', $page);
    }

    public function testCompile()
    {
        $twig = <<<TWIG
{% set page = paged([1, 2, 3, 4, 5], 2, 2) %}
{% for item in page.items %}{{ item }} {% endfor %}
TWIG;

        $env = new \Twig_Environment(new ArrayLoader([
            'index' => $twig,
        ]));
        $env->addExtension(new Extension(new Pager()));

        $this->assertEquals('3 4 ', $env->render('index'));
    }
}
