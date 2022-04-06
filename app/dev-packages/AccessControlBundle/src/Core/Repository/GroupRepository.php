<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\Core\Domain\Entity\Group;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;

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
    public function getAllGroupUsersId(Id $groupId): array
    {
        $connection = $this->_em->getConnection();

        $sql = 'SELECT DISTINCT u.id_value
                FROM "group" g
                JOIN group_user gu on g.id_value = gu.group_id
                JOIN "user" u on u.id_value = gu.user_id
                WHERE g.id_value = ?
                ORDER BY u.id_value';

        return $connection
            ->prepare($sql)
            ->executeQuery([$groupId->value()])
            ->fetchFirstColumn();
    }

    /**
     * Returns array of resources ids, available for specified user.
     */
    public function getAllGroupResourcesId(Id $groupId): array
    {
        $connection = $this->_em->getConnection();

        $sql = 'SELECT DISTINCT r.id_value
                FROM "group" g
                JOIN group_resource gr on g.id_value = gr.group_id
                JOIN resource r on r.id_value = gr.resource_id
                WHERE g.id_value = ?
                ORDER BY r.id_value';

        return $connection
            ->prepare($sql)
            ->executeQuery([$groupId->value()])
            ->fetchFirstColumn();
    }
}
