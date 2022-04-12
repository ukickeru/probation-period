<?php

namespace App\MessageHandler;

use App\Message\OrderConfirmationEmail;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class OrderConfirmationEmailHandler implements MessageHandlerInterface
{
    public function __invoke(OrderConfirmationEmail $orderConfirmationEmail)
    {
        echo 'Sending email for order #' . $orderConfirmationEmail->getOrderId() . PHP_EOL;
    }
}