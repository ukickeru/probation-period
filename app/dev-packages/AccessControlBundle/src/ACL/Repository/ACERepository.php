<?php

namespace Mygento\AccessControlBundle\ACL\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\TransactionIsolationLevel;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\ACL\Domain\Entity\ACE;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACEsCollection;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;
use Mygento\AccessControlBundle\Core\Domain\Service\AccessControlCheckerInterface;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Mygento\AccessControlBundle\Core\Repository\DoctrineRepositoryTrait;

/**
 * @method ACE|null find($id, $lockMode = null, $lockVersion = null)
 * @method ACE|null findOneBy(array $criteria, array $orderBy = null)
 * @method ACE[]    findAll()
 * @method ACE[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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

    /**
     * @param Id $userId     User entity identifier
     * @param Id $resourceId Resource entity identifier
     *
     * @return ACE Found entity object
     */
    public function findById(Id $userId, Id $resourceId): object
    {
        $user = $this->findOneBy(['user' => $userId->value(), 'resource' => $resourceId->value()]);

        if (null === $user) {
            throw new \DomainException($this->_entityName.' with user ID "'.$userId.'" and resource ID "'.$resourceId.'" was not found!');
        }

        return $user;
    }

    public function isResourceAvailableForUser(Id $userId, Id $resourceId): bool
    {
        $qb = $this->_em->createQueryBuilder();

        return null !== $qb->select('ace')
            ->from(ACE::class, 'ace')
            ->where('ace.user = :userId')
            ->andWhere('ace.resource = :resourceId')
            ->setParameters([
                'userId' => $userId->value(),
                'resourceId' => $resourceId->value(),
            ])
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_SCALAR);
    }

    public function updateACLGlobally(ACEsCollection $ACEs): void
    {
        $acl_table_name = $this->_em->getClassMetadata(ACE::class)->getTableName();

        $connection = $this->_em->getConnection();
        $connection->setTransactionIsolation(TransactionIsolationLevel::SERIALIZABLE);

        $connection->beginTransaction();
        try {
            $sql = 'DELETE FROM '.$acl_table_name;
            $connection->prepare($sql)->executeQuery();

            if (count($ACEs) > 0) {
                $connection->beginTransaction();
                try {
                    $sql = 'INSERT INTO '.$acl_table_name.' VALUES '.$ACEs;
                    $connection->prepare($sql)->executeQuery();

                    $connection->commit();
                } catch (\Throwable $exception) {
                    $connection->rollBack();
                    throw new \Exception('Can not insert access control entries.', $exception->getCode(), $exception);
                }
            }

            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();
            throw new \Exception('Can not clear access control entries table.', $exception->getPrevious()->getCode(), $exception);
        }
    }

    public function getACL(): ACEsCollection
    {
        $qb = $this->_em->createQueryBuilder();
        $ACEs = $qb->select('u.id as user_id, r.id as resource_id')
            ->distinct()
            ->from(User::class, 'u')
            ->join('u.groups', 'g')
            ->join('g.resources', 'r')
            ->orderBy('u.id, r.id')
            ->getQuery()
            ->getArrayResult();

        $ACEsCollection = new ACEsCollection();
        foreach ($ACEs as $ACE) {
            $ACEsCollection->addACE([$ACE['user_id'], $ACE['resource_id']]);
        }

        return $ACEsCollection;
    }
}
