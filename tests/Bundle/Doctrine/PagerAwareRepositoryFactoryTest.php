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

namespace KG\Pager\Tests\Bundle\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Repository\RepositoryFactory;
use KG\Pager\Bundle\Doctrine\PagerAwareInterface;
use KG\Pager\Bundle\Doctrine\PagerAwareRepositoryFactory;
use KG\Pager\PagerInterface;
use PHPUnit\Framework\TestCase;

class PagerAwareRepositoryFactoryTest extends TestCase
{
    public function testPagerSetToPagerAwareRepositories(): void
    {
        $pager = $this->createMock(PagerInterface::class);

        $em = $this->createMock(EntityManagerInterface::class);

        $repository = $this->createMock(PagerAwareInterface::class);
        $repository
            ->expects($this->once())
            ->method('setPager')
            ->with($this->identicalTo($pager))
        ;

        $parent = $this->createMock(RepositoryFactory::class);
        $parent
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->identicalTo($em), 'foo')
            ->willReturn($repository)
        ;

        $factory = new PagerAwareRepositoryFactory($pager, $parent);
        $factory->getRepository($em, 'foo');
    }

    public function testPagerNotSetToNativeRepositories()
    {
        $createMockFn = method_exists($this, 'createMock') ? 'createMock' : 'getMock';

        $pager = $this->createMock(PagerInterface::class);

        $em = $this->createMock(EntityManagerInterface::class);

        $repository = $this->createMock(EntityRepository::class);

        $parent = $this->createMock(RepositoryFactory::class);
        $parent
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->identicalTo($em), 'foo')
            ->willReturn($repository)
        ;

        $factory = new PagerAwareRepositoryFactory($pager, $parent);
        $factory->getRepository($em, 'foo');
    }
}
