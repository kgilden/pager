<?php

namespace KG\Pager\Tests\Bundle\Doctrine;

use KG\Pager\Bundle\Doctrine\PagerAwareRepositoryFactory;

class PagerAwareRepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testPagerSetToPagerAwareRepositories()
    {
        $createMockFn = method_exists($this, 'createMock') ? 'createMock' : 'getMock';

        $pager = $this->$createMockFn('KG\Pager\PagerInterface');

        $em = $this->$createMockFn('Doctrine\ORM\EntityManagerInterface');

        $repository = $this->$createMockFn('KG\Pager\Bundle\Doctrine\PagerAwareInterface');
        $repository
            ->expects($this->once())
            ->method('setPager')
            ->with($this->identicalTo($pager))
        ;

        $parent = $this->$createMockFn('Doctrine\ORM\Repository\RepositoryFactory');
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

        $pager = $this->$createMockFn('KG\Pager\PagerInterface');

        $em = $this->$createMockFn('Doctrine\ORM\EntityManagerInterface');

        $repository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $parent = $this
            ->getMockBuilder('Doctrine\ORM\Repository\RepositoryFactory')
            ->disableOriginalConstructor()
            ->getMock()
        ;

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
