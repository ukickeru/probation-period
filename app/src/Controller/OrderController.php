<?php

namespace App\Controller;

use App\Message\OrderConfirmationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @Route(path="/order/place", name="place-order")
     */
    public function placeOrder(): Response
    {
        $orderId = random_int(0,999);

        $this->messageBus->dispatch(new OrderConfirmationEmail($orderId));

        return new Response(
            'Your new order #' . $orderId . ' have been placed!'
        );
    }
}
