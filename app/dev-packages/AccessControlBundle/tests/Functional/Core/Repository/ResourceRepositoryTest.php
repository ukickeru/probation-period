<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMInvalidArgumentException;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Name;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;

class ResourceRepositoryTest extends BaseRepositoryTestCase
{
    /** @var ResourceRepository */
    protected ?ServiceEntityRepository $repository;

    public function testCascadeOperations()
    {
        $name = new Name('Example');

        $groups = [
            new Group($name),
        ];

        $resource = new Resource($groups);

        // Check that cascading for nested entities is not configured
        try {
            $this->repository->save($resource);
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(ORMInvalidArgumentException::class, $exception);
        }
    }

    protected function getEntityFQCN(): string
    {
        return Resource::class;
    }
}
