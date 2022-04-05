<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;

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
    public function getAllGroupUsersId($groupId): array
    {
        $connection = $this->_em->getConnection();

        $sql = 'SELECT DISTINCT u.id
                FROM "group" g
                JOIN group_user gu on g.id = gu.group_id
                JOIN "user" u on u.id = gu.user_id
                WHERE g.id = ?
                ORDER BY u.id';

        return $connection
            ->prepare($sql)
            ->executeQuery([$groupId])
            ->fetchFirstColumn();
    }

    /**
     * Returns array of resources ids, available for specified user.
     */
    public function getAllGroupResourcesId($groupId): array
    {
        $connection = $this->_em->getConnection();

        $sql = 'SELECT DISTINCT r.id
                FROM "group" g
                JOIN group_resource gr on g.id = gr.group_id
                JOIN resource r on r.id = gr.resource_id
                WHERE g.id = ?
                ORDER BY r.id';

        return $connection
            ->prepare($sql)
            ->executeQuery([$groupId])
            ->fetchFirstColumn();
    }
}
