<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Mygento\AccessControlBundle\ACL\Repository\ACERepository;
use Mygento\AccessControlBundle\Core\Domain\Service\SecurityVoter;
use Mygento\AccessControlBundle\Core\Domain\ValueObject\Id;
use Mygento\AccessControlBundle\Core\Repository\OrganizationRepository;
use Mygento\AccessControlBundle\Core\Repository\ProjectRepository;
use Mygento\AccessControlBundle\Core\Repository\ResourceRepository;
use Mygento\AccessControlBundle\Core\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResourceController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private UserRepository $userRepository;

    private ResourceRepository $resourceRepository;

    private OrganizationRepository $organizationRepository;

    private ProjectRepository $projectRepository;

    private SecurityVoter $securityVoter;

    private ACERepository $ACERepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ACERepository $ACERepository,
        UserRepository $userRepository,
        ResourceRepository $resourceRepository,
        OrganizationRepository $organizationRepository,
        ProjectRepository $projectRepository,
        SecurityVoter $securityVoter
    ) {
        $this->entityManager = $entityManager;
        $this->ACERepository = $ACERepository;
        $this->userRepository = $userRepository;
        $this->resourceRepository = $resourceRepository;
        $this->organizationRepository = $organizationRepository;
        $this->projectRepository = $projectRepository;
        $this->securityVoter = $securityVoter;
    }

    /**
     * @Route(path="/resource", name="resource")
     */
    public function getResource()
    {
        $this->ACERepository->getACL();

        $result1 = $this->userRepository->isResourceAvailableForUser(new Id(1), new Id(1));
        $result2 = $this->userRepository->isResourceAvailableForUser(new Id(1), new Id(2));
        $result3 = $this->userRepository->getResourcesIdAvailableForUser(new Id(1));
        $result4 = $this->userRepository->getResourcesIdAvailableForUser(new Id(2));
        dump($result1, $result2, $result3, $result4);

        $checkResult = $this->securityVoter->isGrantedByCriteria(['project_id' => 1]);
        dump($checkResult);

        return new Response();
    }

    /**
     * @Route(path="/resources/{resourceId}", name="id_resource")
     */
    public function getResourceById(int $resourceId)
    {
        $resource = $this->resourceRepository->findById(new Id($resourceId));

        $this->securityVoter->isGranted($resource->getId());

        dump(
            'User:', $this->getUser(),
            'Resource:', $resource,
        );

        return new Response();
    }

    /**
     * @Route(path="/resources/{organizationId}/{projectId}", name="specified_resource")
     */
    public function getSpecifiedResource(int $organizationId, int $projectId)
    {
        /** @var User $user */
        $user = $this->getUser();

        $organization = $this->organizationRepository->findById(new Id($organizationId));
        $organizationResources = $this->resourceRepository->findBy(['organization' => $organization->getId()->value()]);

        $project = $this->projectRepository->findById(new Id($projectId));
        $projectResources = $this->resourceRepository->findBy(['project' => $project->getId()->value()]);

        $this->securityVoter->isGranted($organizationResources[0]->getId());

        dump(
            'Organization:', $organization,
            'Organization resources:', $organizationResources,
            'Project:', $project,
            'Project resources:', $projectResources,
        );

        return new Response();
    }

    /**
     * @Route(path="/resources", name="resources")
     */
    public function getResources()
    {
        /** @var User $user */
        $user = $this->getUser();

        dump(
            'User:', $user,
            'Groups:', $user->getGroups()->toArray(),
            'Organizations:', $this->organizationRepository->findAll(),
            'Projects:', $this->projectRepository->findAll(),
            'Resources:', $this->resourceRepository->findAll(),
            'My resources:', $this->resourceRepository->getResourcesAvailableForUser($user->getId())
        );

        return new Response();
    }

    /**
     * @Route(path="/unprotected/resources/", name="unprotected_resources")
     */
    public function getResourcesUnprotected()
    {
        dump($this->ACERepository->getACL());

        return new Response();
    }
}
