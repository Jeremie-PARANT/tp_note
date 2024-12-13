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
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
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

    #[Route('/user/profile', name: 'get_profile', methods: ['GET'])]
    public function getProfile(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $email = $request->headers->get('email');

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        $data[] = [
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'roles' => $user->getRoles(),
            'name' => $user->getName(),
            'phone_number' => $user->getPhoneNumber()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/user/update', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $email = $request->headers->get('email');
        $data = json_decode($request->getContent(), true);

        $newEmail = $data['email'];
        $newPassword = $data['password'];
        $newName = $data['name'];
        $newPhoneNumber = $data['phone_number'];
        
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        
        $user->setEmail($newEmail);
        $user->setPassword($hashedPassword);
        $user->setName($newName);
        $user->setPhoneNumber($newPhoneNumber);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'A été mis a jour'], Response::HTTP_OK);
    }

    #[Route('/user/delete', name: 'delete_user', methods: ['DELETE'])]
    public function deleteAccount(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $email = $request->headers->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Account successfully deleted'], Response::HTTP_OK);
    }
}