<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\ORM\ORMException;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACLSynchronizer;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;

trait DoctrineRepositoryTrait
{
    protected ?ACLSynchronizer $ACLSynchronizer = null;

    /**
     * @param Id $id Unified entity identifier
     *
     * @return object Found entity object
     */
    public function findById(Id $id): object
    {
        $user = $this->findOneBy(['id' => $id->value()]);

        if (null === $user) {
            throw new \DomainException($this->_entityName.' with ID "'.$id.'" was not found!');
        }

        return $user;
    }

    /**
     * @param object $object Entity object to persist
     *
     * @throws \DomainException in case of entity class was not match for declared
     */
    public function save(object $object): void
    {
        if (!($object instanceof $this->_entityName)) {
            throw new \DomainException(self::class.' works only with "'.$this->_entityName.'" objects!');
        }

        $this->_em->persist($object);
        $this->_em->flush();

        if ($this->ACLSynchronizer instanceof ACLSynchronizer) {
            $this->ACLSynchronizer->synchronize();
        }
    }

    /**
     * @param object $object Entity object to update
     *
     * @throws \DomainException in case of entity class was not match for declared
     */
    public function update(object $object): void
    {
        if (!($object instanceof $this->_entityName)) {
            throw new \DomainException(self::class.' works only with "'.$this->_entityName.'" objects!');
        }

        $this->_em->persist($object);
        $this->_em->flush();

        if ($this->ACLSynchronizer instanceof ACLSynchronizer) {
            $this->ACLSynchronizer->synchronize();
        }
    }

    /**
     * @param Id|object $entityOrId Entity object or it's id
     *
     * @throws \DomainException in case of entity class was not match for declared or $id is not a scalar
     * @throws ORMException     in case of entity was not found
     */
    public function remove($entityOrId): void
    {
        if (!($entityOrId instanceof Id) && !($entityOrId instanceof $this->_entityName)) {
            throw new \DomainException(self::class.' works only with "'.$this->_entityName.'" objects!');
        }

        if ($entityOrId instanceof Id) {
            $entityOrId = $this->_em->getReference($this->_entityName, $entityOrId->value());
        }

        $this->_em->remove($entityOrId);
        $this->_em->flush();

        if ($this->ACLSynchronizer instanceof ACLSynchronizer) {
            $this->ACLSynchronizer->synchronize();
        }
    }
}
