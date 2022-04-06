<?php

namespace Mygento\AccessControlBundle\Tests\Functional\Core\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

trait SchemaRecreationTrait
{
    public function recreateSchema(EntityManagerInterface $entityManager)
    {
        // Drop and recreate tables for all entities
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function dropSchema(EntityManagerInterface $entityManager)
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
    }
}
