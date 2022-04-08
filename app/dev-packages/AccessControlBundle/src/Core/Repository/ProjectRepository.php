<?php

namespace Mygento\AccessControlBundle\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mygento\AccessControlBundle\ACL\Domain\Service\ACLSynchronizer;
use Mygento\AccessControlBundle\Core\Domain\Entity\Project;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Project      findById($id)
 * @method void         save(Project $object)
 * @method void         update(Project $object)
 * @method void         remove($entityOrId)
 */
class ProjectRepository extends ServiceEntityRepository
{
    use DoctrineRepositoryTrait;

    public function __construct(
        ManagerRegistry $registry,
        ACLSynchronizer $ACLSynchronizer
    ) {
        parent::__construct($registry, Project::class);
        $this->ACLSynchronizer = $ACLSynchronizer;
    }
}
