<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\Service\AccessControlCheckerInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @method User      findById($id)
 * @method void      save(object $object)
 * @method void      update(object $object)
 * @method void      remove($entityOrId)
 */
class UserRepository extends ServiceEntityRepository implements AccessControlCheckerInterface
{
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAllUserResources($id)
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

        return $this->_em->createNativeQuery($sql, $rsm)
            ->setParameter('userId', $id)
            ->getArrayResult();
    }

    public function isResourceAvailableForUser($userId, $resourceId): bool
    {
        $rsm = (new ResultSetMappingBuilder($this->_em))
            ->addScalarResult('user_id', 'user_id')
            ->addScalarResult('resource_id', 'resource_id');

        $sql = 'SELECT (u.id) as user_id, (r.id) as resource_id' .
            'FROM resources r' .
            'JOIN group_resources g_r ON r.id = g_r.resource_id' .
            'JOIN group g ON g.id = g_r.group_id' .
            'JOIN users_groups u_g ON g.id = u_g.group_id' .
            'JOIN users u ON u.id = u_g.user_id' .
            'WHERE u.id = :userId' .
            'AND WHERE r.id = :resourceId'
        ;

        return $this->_em->createNativeQuery($sql, $rsm)
            ->setParameter('userId', $userId)
            ->setParameter('resourceId', $resourceId)
            ->getArrayResult();
    }
}
