<?php

namespace App\Controller;

use App\Entity\Reservation;
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

class ReservationController extends AbstractController
{
    #[Route('/user/reservation', name: 'create_reservation', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $email = $request->headers->get('email');
        $data = json_decode($request->getContent(), true);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        $date = new \DateTime($data['date']);
        $timeSlot = $data['time_slot'];
        $eventName = $data['event_name'];

        $reservation = new Reservation();
        
        $reservation->setDate($date);
        $reservation->setTimeSlot($timeSlot);
        $reservation->setEventName($eventName);
        $reservation->setUser($user);

        $entityManager->persist($reservation);
        $entityManager->flush();

        return new JsonResponse(['message' => 'reservation create'], Response::HTTP_CREATED);
    }
}