<?php

namespace Mygento\AccessControlBundle\Core\Domain\Exception;

use Throwable;

class AccessDeniedException extends \Exception
{
    public function __construct($userId, $resourceId, Throwable $previous = null)
    {
        $message = 'Access to resource with ID "' . $resourceId . '" for user "' . $userId . '" is denied!';

        parent::__construct($message, 403, $previous);
    }
}
