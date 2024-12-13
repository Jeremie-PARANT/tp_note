<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserController extends AbstractController
{
    #[Route('/register', name: 'view_profile', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $password = $data['password'];
        $roles = $data['roles'];
        $name = $data['name'];
        $phoneNumber = $data['phone_number'];

        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        
        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setRoles($roles);
        $user->setName($name);
        $user->setPhoneNumber($phoneNumber);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User create'], Response::HTTP_CREATED);
    }

    #[Route('/user/api_token_test', name: 'api_token_test', methods: ['GET'])]
    public function apiTokenWorks(): JsonResponse
    {
        return new JsonResponse("Is connected as user", Response::HTTP_OK);
    }
}