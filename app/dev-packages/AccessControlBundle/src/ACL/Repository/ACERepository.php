<?php

namespace Mygento\AccessControlBundle\ACL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\ACL\Entity\ACE;
use Mygento\AccessControlBundle\Core\Domain\Service\AccessControlCheckerInterface;
use Mygento\AccessControlBundle\Core\Repository\DoctrineRepositoryTrait;

/**
 * @method ACE|null find($id, $lockMode = null, $lockVersion = null)
 * @method ACE|null findOneBy(array $criteria, array $orderBy = null)
 * @method ACE[]    findAll()
 * @method ACE[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @method ACE      findById($id)
 * @method void     save(object $object)
 * @method void     update(object $object)
 * @method void     remove($entityOrId)
 */
class ACERepository extends ServiceEntityRepository implements AccessControlCheckerInterface
{
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ACE::class);
    }

    public function isResourceAvailableForUser($userId, $resourceId): bool
    {
        return null !== $this->findOneBy([
            'userId' => $userId,
            'resourceId' => $resourceId
        ]);
    }

    public function synchronizeUserACL($userId): void
    {
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('id', 'id');

        $sql = 'SELECT (r.id) as id' .
            'FROM resources r' .
            'JOIN group_resources g_r ON r.id = g_r.resource_id' .
            'JOIN group g ON g.id = g_r.group_id' .
            'JOIN users_groups u_g ON g.id = u_g.group_id' .
            'JOIN users u ON u.id = u_g.user_id' .
            'WHERE u.id = :userId'
        ;

        $userResourcesIds = $this->_em->createNativeQuery($sql, $rsm)
            ->setParameter('userId', $userId)
            ->getArrayResult();

        $qb = $this->_em->createQueryBuilder();
        $ACEsThatAlreadyExists = $qb->select('resourceId')
            ->from(ACE::class, 'ace')
            ->where($qb->expr()->in('ace.resourceId', $userResourcesIds))
            ->getQuery()
            ->getArrayResult();

        $resourcesToCreateACE = array_diff($ACEsThatAlreadyExists, $userResourcesIds);

        // todo
    }

    public function synchronizeGroupACL($groupId)
    {
        // todo
    }

    public function synchronizeResourceACL($userId)
    {
        // todo
    }
}
