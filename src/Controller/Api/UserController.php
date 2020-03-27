<?php

namespace App\Controller\Api;

use App\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractApiController
{
    /**
     * @Route("/users/me", name="users.current")
     * @return Response
     */
    public function current(): Response
    {
        return $this->json($this->getUser());
    }
}