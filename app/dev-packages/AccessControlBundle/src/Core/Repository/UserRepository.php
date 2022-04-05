<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
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

    public function getAllUsersId(): array
    {
        $connection = $this->_em->getConnection();
        $sql = 'SELECT DISTINCT u.id
                FROM "user" u
                ORDER BY u.id';

        return $connection
            ->prepare($sql)
            ->executeQuery()
            ->fetchFirstColumn();
    }

    /**
     * Returns array of resources ids, available for specified user.
     */
    public function getResourcesIdAvailableForUser($userId): array
    {
        $connection = $this->_em->getConnection();
        $sql = 'SELECT DISTINCT r.id
                FROM resource r
                JOIN group_resource gr on r.id = gr.resource_id
                JOIN "group" g on g.id = gr.group_id
                JOIN group_user gu on g.id = gu.group_id
                JOIN "user" u on u.id = gu.user_id
                WHERE u.id = ?
                ORDER BY r.id';

        return $connection
            ->prepare($sql)
            ->executeQuery([$userId])
            ->fetchFirstColumn();
    }

    /**
     * Checks if specified user has access to specified resource.
     */
    public function isResourceAvailableForUser($userId, $resourceId): bool
    {
        try {
            $connection = $this->_em->getConnection();
            $sql = 'SELECT DISTINCT r.id
                FROM resource r
                JOIN group_resource gr on r.id = gr.resource_id
                JOIN "group" g on g.id = gr.group_id
                JOIN group_user gu on g.id = gu.group_id
                JOIN "user" u on u.id = gu.user_id
                WHERE u.id = ? AND r.id = ?
                ORDER BY r.id';

            $ace = $connection
                ->prepare($sql)
                ->executeQuery([$userId, $resourceId])
                ->fetchFirstColumn();
        } catch (\Throwable $throwable) {
            return false;
        }

        if (empty($ace)) {
            return false;
        }

        return true;
    }
}
