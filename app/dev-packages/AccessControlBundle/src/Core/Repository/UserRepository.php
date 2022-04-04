<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\Service\AccessControlCheckerInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method User      findById($id)
 * @method void      save(User $object)
 * @method void      update(User $object)
 * @method void      remove($entityOrId)
 */
class UserRepository extends ServiceEntityRepository implements AccessControlCheckerInterface
{
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Returns array of resources ids, available for specified user.
     */
    public function getAllUserResources($userId): array
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb->select('(r.id) as id')
            ->distinct()
            ->from(Resource::class, 'r')
            ->join('r.groups', 'g')
            ->join('g.users', 'u')
            ->where(
                $qb->expr()->eq('u.id', $userId)
            )
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Checks if specified user has access to specified resource.
     */
    public function isResourceAvailableForUser($userId, $resourceId): bool
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            $ace = $qb->select('(r.id) as id')
                ->distinct()
                ->from(Resource::class, 'r')
                ->join('r.groups', 'g')
                ->join('g.users', 'u')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('u.id', $userId),
                        $qb->expr()->eq('r.id', $resourceId)
                    )
                )
                ->getQuery()
                ->getSingleScalarResult();
        } catch (\Throwable) {
            return false;
        }

        if (empty($ace)) {
            return false;
        }

        return true;
    }
}
