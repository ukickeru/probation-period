<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACLSynchronizer;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;

/**
 * @method \Mygento\AccessControlBundle\Core\Domain\Entity\Resource|null find($id, $lockMode = null, $lockVersion = null)
 * @method \Mygento\AccessControlBundle\Core\Domain\Entity\Resource|null findOneBy(array $criteria, array $orderBy = null)
 * @method \Mygento\AccessControlBundle\Core\Domain\Entity\Resource[]    findAll()
 * @method \Mygento\AccessControlBundle\Core\Domain\Entity\Resource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method \Mygento\AccessControlBundle\Core\Domain\Entity\Resource      findById($id)
 * @method void                                                          save(\Mygento\AccessControlBundle\Core\Domain\Entity\Resource $object)
 * @method void                                                          update(\Mygento\AccessControlBundle\Core\Domain\Entity\Resource $object)
 * @method void                                                          remove($entityOrId)
 */
class ResourceRepository extends ServiceEntityRepository
{
    use DoctrineRepositoryTrait;

    public function __construct(
        ManagerRegistry $registry,
        ACLSynchronizer $ACLSynchronizer
    ) {
        parent::__construct($registry, Resource::class);
        $this->ACLSynchronizer = $ACLSynchronizer;
    }

    /**
     * Returns array of resources, available for specified user.
     */
    public function getResourcesAvailableForUser(Id $userId): array
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb->select('r')
            ->from(Resource::class, 'r')
            ->join('r.groups', 'g')
            ->join('g.users', 'u')
            ->where($qb->expr()->eq('u.id', ':userId'))
            ->orderBy('r.id')
            ->setParameter('userId', $userId->value())
            ->getQuery()
            ->getResult();
    }
}
