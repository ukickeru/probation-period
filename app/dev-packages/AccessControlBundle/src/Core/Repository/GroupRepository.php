<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Group      findById($id)
 * @method void       save(Group $object)
 * @method void       update(Group $object)
 * @method void       remove($entityOrId)
 */
class GroupRepository extends ServiceEntityRepository
{
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    /**
     * Returns array of resources ids, available for specified user.
     */
    public function getAllGroupUserIds($groupId): array
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb->select('(u.id) as id')
            ->distinct()
            ->from(User::class, 'u')
            ->join('u.groups', 'g')
            ->where(
                $qb->expr()->eq('g.id', ':groupId')
            )
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Returns array of resources ids, available for specified user.
     */
    public function getAllGroupResourceIds($groupId): array
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb->select('(r.id) as id')
            ->distinct()
            ->from(Resource::class, 'r')
            ->join('r.groups', 'g')
            ->where(
                $qb->expr()->eq('g.id', ':groupId')
            )
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getSingleColumnResult();
    }
}
