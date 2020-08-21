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

namespace KG\Pager\Tests\Bundle\DependencyInjection;

use KG\Pager\BoundsCheckDecorator;
use KG\Pager\Bundle\DependencyInjection\KGPagerExtension;
use KG\Pager\PagerInterface;
use KG\Pager\PagingStrategy\LastPageMerged;
use KG\Pager\RequestDecorator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Yaml\Parser;

class KGPagerExtensionTest extends TestCase
{
    public function testDefaultPagerRegistered(): void
    {
        $container = $this->createContainer('');

        $this->assertInstanceOf(PagerInterface::class, $container->get('kg_pager'));
        $this->assertSame($container->get('kg_pager'), $container->get('kg_pager.pager.default'));
    }

    public function testPerPageSet()
    {
        $yaml = <<<YAML
pagers:
    default:
        per_page: 15
        key: ~
        redirect: false
YAML;

        $definition = $this
            ->createContainer($yaml)
            ->findDefinition('kg_pager.pager.default')
        ;

        $this->assertEquals(15, $definition->getArgument(0));
    }

    public function testPagerWrappedInRequestDecoratorIfCurrentPageSet(): void
    {
        $yaml = <<<YAML
pagers:
    default:
        key: my_page
        redirect: false
YAML;

        $pager = $this
            ->createContainer($yaml)
            ->get('kg_pager.pager.default')
        ;

        $this->assertInstanceOf(RequestDecorator::class, $pager);
    }

    public function testPagerNotWrappedInRequestDecoratorIfCurrentPageNotSet(): void
    {
        $yaml = <<<YAML
pagers:
    default:
        key: ~
        redirect: false
YAML;

        $definition = $this
            ->createContainer($yaml)
            ->getDefinition('kg_pager.pager.default')
        ;

        $this->assertNotEquals(RequestDecorator::class, $definition->getClass());
    }

    public function testPagerWrappedInBoundsCheckDecorator(): void
    {
        $yaml = <<<YAML
pagers:
    default:
        key: foo
        redirect: true
YAML;

        $container = $this->createContainer($yaml);
        $definition = $container->getDefinition('kg_pager.pager.default');

        $this->assertEquals(BoundsCheckDecorator::class, $definition->getClass());

        $refl = new \ReflectionClass($definition->getArgument(0)->getClass());
        $this->assertTrue($refl->implementsInterface(PagerInterface::class));
        $this->assertEquals('foo', $definition->getArgument(1));

        $this->assertTrue($container->has('kg_pager.out_of_bounds_redirector'));
    }

    public function testRedirectorRemovedIfNoPagersShouldBeRedirected(): void
    {
        $yaml = <<<YAML
pagers:
    default:
        key: ~
        redirect: false
YAML;

        $container = $this->createContainer($yaml);

        $this->assertFalse($container->has('kg_pager.out_of_bounds_redirector'));
    }

    public function testMergeStrategyNotUsedByDefault(): void
    {
$yaml = <<<YAML
pagers:
    default:
        key: ~
YAML;

        $arguments = $this
            ->createContainer($yaml)
            ->getDefinition('kg_pager.pager.default')
            ->getArguments()
        ;

        $this->assertTrue(!isset($arguments[1]) || is_null($arguments[1]));
    }

    public function testMergeStrategyUsedIfMergeNotNull(): void
    {
$yaml = <<<YAML
pagers:
    default:
        key: ~
        merge: 0.333
        redirect: false
YAML;

        $definition = $this
            ->createContainer($yaml)
            ->getDefinition('kg_pager.pager.default')
            ->getArgument(1)
        ;

        $this->assertNotNull($definition);

        $this->assertEquals(LastPageMerged::class, $definition->getClass());
        $this->assertEquals(0.333, $definition->getArgument(0));
    }

    private function createContainer(string $yaml): ContainerBuilder
    {
        $parser = new Parser();
        $container = new ContainerBuilder();
        $container->register('request_stack', RequestStack::class);

        $loader = new KGPagerExtension();
        $loader->load(array($parser->parse($yaml)), $container);

        return $container;
    }
}
