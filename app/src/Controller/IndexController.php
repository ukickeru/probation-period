<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route(path="/", name="index")
     */
    public function index(): Response
    {
        return $this->render('app.html.twig');
    }

    /**
     * @Route(path="/app", name="spa_index")
     */
    public function app(): Response
    {
        return $this->render('app.html.twig');
    }
}
