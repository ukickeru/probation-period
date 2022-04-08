<?php

namespace Mygento\AccessControlBundle\ACL\Domain\Service;

use Mygento\AccessControlBundle\ACL\Repository\ACERepository;

class ACLSynchronizer
{
    private ACERepository $ACERepository;

    public function __construct(
        ACERepository $ACERepository
    ) {
        $this->ACERepository = $ACERepository;
    }

    public function synchronize(): void
    {
        $ACEs = $this->ACERepository->getACL();

        $this->ACERepository->updateACLGlobally($ACEs);
    }
}
