<?php

namespace Mygento\AccessControlBundle\Core\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Mygento\AccessControlBundle\Core\Domain\Entity\User;

class DoctrineMetadataListener
{
    private string $appUserEntityTableName;

    public function __construct(
        string $appUserEntityTableName
    ) {
        $this->appUserEntityTableName = $appUserEntityTableName;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();

        if (User::class !== $classMetadata->getName()) {
            return;
        }

        $classMetadata->setPrimaryTable(['name' => $this->appUserEntityTableName]);
    }
}
