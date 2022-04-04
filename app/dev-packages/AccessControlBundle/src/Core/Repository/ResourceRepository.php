<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\Core\Domain\Entity\Resource;

/**
 * @method resource|null find($id, $lockMode = null, $lockVersion = null)
 * @method resource|null findOneBy(array $criteria, array $orderBy = null)
 * @method resource[]    findAll()
 * @method resource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method resource      findById($id)
 * @method void          save(Resource $object)
 * @method void          update(Resource $object)
 * @method void          remove($entityOrId)
 */
class ResourceRepository extends ServiceEntityRepository
{
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resource::class);
    }
}
