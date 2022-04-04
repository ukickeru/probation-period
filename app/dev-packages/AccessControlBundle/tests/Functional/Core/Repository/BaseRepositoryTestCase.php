<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseRepositoryTestCase extends KernelTestCase
{
    protected ?EntityManagerInterface $entityManager;

    protected ?SchemaTool $schemaTool;

    protected ?ServiceEntityRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository($this->getEntityFQCN());

        // Drop and recreate tables for all entities
        $this->schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $this->schemaTool->dropSchema($metadata);
        $this->schemaTool->createSchema($metadata);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
        $this->schemaTool = null;
        $this->repository = null;
    }

    abstract protected function getEntityFQCN(): string;
}
