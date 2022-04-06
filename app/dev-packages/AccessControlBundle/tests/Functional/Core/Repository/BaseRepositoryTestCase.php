<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseRepositoryTestCase extends KernelTestCase
{
    use SchemaRecreationTrait;

    protected ?EntityManagerInterface $entityManager;

    protected ?ServiceEntityRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository($this->getEntityFQCN());

        $this->recreateSchema($this->entityManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // to avoid memory leaks
        $this->dropSchema($this->entityManager);
        $this->entityManager->close();
        $this->entityManager = null;
        $this->repository = null;
    }

    abstract protected function getEntityFQCN(): string;
}
