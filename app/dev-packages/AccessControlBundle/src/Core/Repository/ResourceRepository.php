<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;

/**
 * @method Resource|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resource|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resource[]    findAll()
 * @method Resource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Resource      findById($id)
 * @method void          save(Resource $object)
 * @method void          update(Resource $object)
 * @method void          remove($entityOrId)
 */
class ResourceRepository extends ServiceEntityRepository
{
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resource::class);
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
            ->where($qb->expr()->eq('u.id.value', ':userId'))
            ->orderBy('r.id.value')
            ->setParameter('userId', $userId->value())
            ->getQuery()
            ->getResult();
    }
}
