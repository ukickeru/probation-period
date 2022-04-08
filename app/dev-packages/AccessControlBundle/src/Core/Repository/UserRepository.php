<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACLSynchronizer;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\Service\AccessControlCheckerInterface;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;

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

    public function __construct(
        ManagerRegistry $registry,
        ACLSynchronizer $ACLSynchronizer,
        string $entityClass = User::class
    ) {
        parent::__construct($registry, $entityClass);
        $this->ACLSynchronizer = $ACLSynchronizer;
    }

    /**
     * Checks if specified user has access to specified resource.
     */
    public function isResourceAvailableForUser(Id $userId, Id $resourceId): bool
    {
        try {
            $qb = $this->_em->createQueryBuilder();

            return null !== $qb->select('r.id')
                    ->from(Resource::class, 'r')
                    ->join('r.groups', 'g')
                    ->join('g.users', 'u')
                    ->where('u.id = :userId')
                    ->andWhere('r.id = :resourceId')
                    ->setParameters([
                        'userId' => $userId->value(),
                        'resourceId' => $resourceId->value(),
                    ])
                    ->getQuery()
                    ->getOneOrNullResult(AbstractQuery::HYDRATE_SCALAR);
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    /**
     * Returns array of resources ids, available for specified user.
     */
    public function getResourcesIdAvailableForUser(Id $userId): array
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb->select('r.id')
                ->from(Resource::class, 'r')
                ->join('r.groups', 'g')
                ->join('g.users', 'u')
                ->where('u.id = :userId')
                ->setParameter('userId', $userId->value())
                ->getQuery()
                ->getSingleColumnResult();
    }
}
