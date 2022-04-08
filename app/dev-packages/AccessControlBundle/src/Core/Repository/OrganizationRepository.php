<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACLSynchronizer;
use Mygento\AccessControlBundle\Core\Domain\Entity\Organization;

/**
 * @method Organization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organization[]    findAll()
 * @method Organization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Organization      findById($id)
 * @method void              save(Organization $object)
 * @method void              update(Organization $object)
 * @method void              remove($entityOrId)
 */
class OrganizationRepository extends ServiceEntityRepository
{
    use DoctrineRepositoryTrait;

    public function __construct(
        ManagerRegistry $registry,
        ACLSynchronizer $ACLSynchronizer
    ) {
        parent::__construct($registry, Organization::class);
        $this->ACLSynchronizer = $ACLSynchronizer;
    }
}
