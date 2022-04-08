<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\ACL\Domain\Entity\ACE;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACLSynchronizer;
use Mygento\AccessControlBundle\Core\Domain\Service\CustomAccessControlCheckerInterface;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Mygento\AccessControlBundle\Core\Repository\UserRepository as AccessControlUserRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AccessControlUserRepository implements CustomAccessControlCheckerInterface
{
    public function __construct(
        ManagerRegistry $registry,
        ACLSynchronizer $ACLSynchronizer
    ) {
        parent::__construct($registry, $ACLSynchronizer, User::class);
    }

    public function isResourceAvailableForUserByCriteria(Id $userId, $criteria): bool
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('r.id')
            ->from(ACE::class, 'ace')
            ->join('ace.resource', 'r')
            ->where('ace.user = :userId')
            ->setParameter('userId', $userId->value());

        if (isset($criteria['organization_id'])) {
            $qb->andWhere('r.organization = :organizationId')
                ->setParameter('organizationId', $criteria['organization_id']);
        }

        if (isset($criteria['project_id'])) {
            $qb->andWhere('r.project = :projectId')
                ->setParameter('projectId', $criteria['project_id']);
        }

        return null !== $qb
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_SCALAR);
    }
}
