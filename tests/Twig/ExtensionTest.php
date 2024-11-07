<?php

declare(strict_types=1);

namespace KG\Pager\Tests\Twig;

use KG\Pager\PageInterface;
use KG\Pager\Pager;
use KG\Pager\Twig\Extension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class ExtensionTest extends TestCase
{
    public function testPageReturned(): void
    {
        $extension = new Extension(new Pager());

        $page = $extension->paged(['a', 'b', 'c']);

        $this->assertInstanceOf(PageInterface::class, $page);
    }

    public function testCompile(): void
    {
        $twig = <<<TWIG
{% set page = paged([1, 2, 3, 4, 5], 2, 2) %}
{% for item in page.items %}{{ item }},{% endfor %}
TWIG;

        $env = new Environment(new ArrayLoader([
            'index' => $twig,
        ]));
        $env->addExtension(new Extension(new Pager()));

        $this->assertEquals('3,4,', $env->render('index'));
    }
}
