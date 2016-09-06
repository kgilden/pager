<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager\Bundle\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Doctrine\ORM\Repository\RepositoryFactory;
use KG\Pager\PagerInterface;

/**
 * Injects the given pager to newly created entity repositories, which adhere
 * to the PagerAwareInterface.
 */
class PagerAwareRepositoryFactory implements RepositoryFactory
{
    /**
     * @var RepositoryFactory|null
     */
    private $factory;

    /**
     * @var PagerInterface
     */
    private $pager;

    /**
     * @param PagerInterface         $pager
     * @param RepositoryFactory|null $factory
     */
    public function __construct(PagerInterface $pager, RepositoryFactory $factory = null)
    {
        $this->factory = $factory;
        $this->pager = $pager;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(EntityManagerInterface $entityManager, $entityName)
    {
        if (!$this->factory) {
            $this->factory = new DefaultRepositoryFactory();
        }

        $repository = $this->factory->getRepository($entityManager, $entityName);

        if ($repository instanceof PagerAwareInterface) {
            $repository->setPager($this->pager);
        }

        return $repository;
    }
}
