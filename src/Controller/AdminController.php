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

class AdminController extends AbstractController
{
    #[Route('/admin/user', name: 'get_all_users', methods: ['GET'])]
    public function getAllUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
           $data[] = [
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'roles' => $user->getRoles(),
            'name' => $user->getName(),
            'phone_number' => $user->getPhoneNumber()
            ]; 
        }
        
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/admin/user', name: 'admin_update_user', methods: ['PUT'])]
    public function updateUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $id = $data['id'];
        $newEmail = $data['email'];
        $newPassword = $data['password'];
        $newName = $data['name'];
        $newPhoneNumber = $data['phone_number'];
        
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        
        $user->setEmail($newEmail);
        $user->setPassword($hashedPassword);
        $user->setName($newName);
        $user->setPhoneNumber($newPhoneNumber);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'A été mis a jour'], Response::HTTP_OK);
    }

    #[Route('/admin/user', name: 'admin_delete_user', methods: ['DELETE'])]
    public function deleteAccount(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];

        $user = $userRepository->findOneBy(['id' => $id]);

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Account successfully deleted'], Response::HTTP_OK);
    }
}