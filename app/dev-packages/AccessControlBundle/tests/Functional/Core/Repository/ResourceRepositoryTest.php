<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;

class ResourceRepositoryTest extends BaseRepositoryTestCase
{
    /** @var ResourceRepository */
    protected ?ServiceEntityRepository $repository;

    protected function getEntityFQCN(): string
    {
        return Resource::class;
    }
}
