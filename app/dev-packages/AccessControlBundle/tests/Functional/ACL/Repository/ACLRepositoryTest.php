<?php

namespace Mygento\AccessControlBundle\Tests\Functional\ACL\Repository;

use Mygento\AccessControlBundle\ACL\Entity\ACE;
use Mygento\AccessControlBundle\Tests\Functional\Core\Repository\BaseRepositoryTestCase;

class ACLRepositoryTest extends BaseRepositoryTestCase
{
    protected function getEntityFQCN(): string
    {
        return ACE::class;
    }
}
