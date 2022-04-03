<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\ORM\ORMException;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;

/**
 * Trait DoctrineRepositoryTrait is intended to shorten code that uses built-in doctrine mechanisms
 * @package Mygento\AccessControlBundle\Core\Repository
 */
trait DoctrineRepositoryTrait
{
    /**
     * @param scalar $id Unified entity identifier
     * @return object Found entity object
     * @throws \DomainException in case of entity was not found or $id is not a scalar
     */
    public function findById($id): object
    {
        if (!is_scalar($id)) {
            throw new \DomainException('Id must be represent by positive integer value!');
        }

        /** @var User|null $user */
        $user = $this->findOneBy(['id' => $id]);

        if ($user === null) {
            throw new \DomainException('User with ID "' . $id . '" was not found!');
        }

        return $user;
    }

    /**
     * @param object $object Entity object to persist
     * @throws \DomainException in case of entity class was not match for declared
     */
    public function save(object $object): void
    {
        if (!($object instanceof $this->_entityName)) {
            throw new \DomainException(self::class . ' works only with "' . $this->_entityName . '" objects!');
        }

        $this->_em->persist($object);
        $this->_em->flush();
    }

    /**
     * @param object $object Entity object to update
     * @throws \DomainException in case of entity class was not match for declared
     */
    public function update(object $object): void
    {
        if (!($object instanceof $this->_entityName)) {
            throw new \DomainException(self::class . ' works only with "' . $this->_entityName . '" objects!');
        }

        $this->_em->persist($object);
        $this->_em->flush();
    }

    /**
     * @param object $entityOrId Entity object or it's id to remove
     * @throws \DomainException in case of entity class was not match for declared or $id is not a scalar
     * @throws ORMException in case of entity was not found
     */
    public function remove($entityOrId): void
    {
        if (is_scalar($entityOrId)) {
            $entityOrId = $this->_em->getReference($this->_entityName, $entityOrId);
        } elseif (!($entityOrId instanceof $this->_entityName)) {
            throw new \DomainException(self::class . ' works only with "' . $this->_entityName . '" objects!');
        } else {
            throw new \DomainException('Id must be represent by positive integer value!');
        }

        $this->_em->remove($entityOrId);
        $this->_em->flush();
    }
}
