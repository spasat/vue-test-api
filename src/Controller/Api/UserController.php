<?php

namespace App\Controller\Api;

use App\Controller\AbstractApiController;
use App\Entity\User;
use App\Form\UserRegisterType;
use App\Form\UserUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationExpiredException;

/**
 * @Route("/users")
 * Class UserController
 * @package App\Controller\Api
 */
class UserController extends AbstractApiController
{
    /**
     * @Route("/me", name="users.current", methods={"GET"})
     * @return Response
     */
    public function current(): Response
    {
        return $this->json($this->getUser());
    }

    /**
     * @Route("/register", name="users.register", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder
    ): Response
    {
        $form = $this->createForm(UserRegisterType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $encodedPassword = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);

            $em->persist($user);
            $em->flush();

            return new Response(null, 201);
        }

        return $this->json(
            [
                'code' => 400,
                'errors' => $this->getValidationErrors($form)
            ],
            400
        );
    }

    /**
     * @Route("/profile", name="users.update", methods={"PUT"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function update(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserUpdateType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();
            return $this->json($user, 200);
        }

        return $this->json(
            [
                'code' => 400,
                'errors' => $this->getValidationErrors($form)
            ],
            400
        );
    }
}