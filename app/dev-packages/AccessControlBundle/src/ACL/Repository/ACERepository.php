<?php

namespace Mygento\AccessControlBundle\ACL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\TransactionIsolationLevel;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\ACL\Domain\Entity\ACE;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACEsCollection;
use Mygento\AccessControlBundle\Core\Domain\Service\AccessControlCheckerInterface;
use Mygento\AccessControlBundle\Core\Repository\DoctrineRepositoryTrait;
use Mygento\AccessControlBundle\Core\Repository\GroupRepository;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;

/**
 * @method ACE|null find($id, $lockMode = null, $lockVersion = null)
 * @method ACE|null findOneBy(array $criteria, array $orderBy = null)
 * @method ACE[]    findAll()
 * @method ACE[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method ACE      findById($id)
 * @method void     save(object $object)
 * @method void     update(object $object)
 * @method void     remove($entityOrId)
 */
class ACERepository extends ServiceEntityRepository implements AccessControlCheckerInterface
{
    use DoctrineRepositoryTrait;

    private UserRepository $userRepository;

    private GroupRepository $groupRepository;

    public function __construct(
        ManagerRegistry $registry,
        UserRepository $userRepository,
        GroupRepository $groupRepository
    ) {
        parent::__construct($registry, ACE::class);

        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
    }

    public function isResourceAvailableForUser($userId, $resourceId): bool
    {
        $connection = $this->_em->getConnection();

        $sql = 'SELECT ace.resource_id FROM ace WHERE ace.user_id = ? AND ace.resource_id = ?';

        return 1 === $connection->prepare($sql)->executeQuery([$userId, $resourceId])->rowCount();
    }

    public function getResourcesIdAvailableForUser($userId): array
    {
        $connection = $this->_em->getConnection();

        $sql = 'SELECT ace.resource_id FROM ace WHERE ace.user_id = ?';

        return $connection->prepare($sql)->executeQuery([$userId])->fetchFirstColumn();
    }

    public function updateACEs(
        ACEsCollection $ACEsToInsert,
        ACEsCollection $ACEsToRemove
    ): void {
        $connection = $this->_em->getConnection();
        $connection->setTransactionIsolation(TransactionIsolationLevel::SERIALIZABLE);

        $connection->beginTransaction();
        try {
            $sql = 'INSERT INTO ace (user_id, resource_id) VALUES '.$ACEsToInsert;
            $connection->prepare($sql)->executeQuery();

            $connection->beginTransaction();
            try {
                $sql = 'DELETE FROM ace WHERE (user_id, resource_id) IN ('.$ACEsToRemove.')';
                $connection->prepare($sql)->executeQuery();

                $connection->commit();
            } catch (\Throwable $exception) {
                $connection->rollBack();
                throw new \Exception('Can not remove access control entries.', $exception->getCode(), $exception);
            }

            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();
            throw new \Exception('Can not insert access control entries.', $exception->getPrevious()->getCode(), $exception);
        }
    }

    public function insertACEs(ACEsCollection $ACEs)
    {
        $connection = $this->_em->getConnection();

        $sql = 'INSERT INTO ace (user_id, resource_id) VALUES '.$ACEs;

        $connection->prepare($sql)->executeQuery();
    }

    public function removeACEs(ACEsCollection $ACEs)
    {
        $connection = $this->_em->getConnection();

        $sql = 'DELETE FROM ace WHERE (ace.user_id, ace.resource_id) IN ('.$ACEs.')';

        $connection->prepare($sql)->executeQuery();
    }
}
