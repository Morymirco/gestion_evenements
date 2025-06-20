<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Repository\RegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        EventRepository $eventRepository,
        UserRepository $userRepository,
        RegistrationRepository $registrationRepository
    ): Response {
        // Statistiques pour la page d'accueil
        $stats = [
            'total_events' => $eventRepository->count([]),
            'active_events' => $eventRepository->count(['isActive' => true]),
            'total_users' => $userRepository->count([]),
            'total_registrations' => $registrationRepository->count([]),
        ];

        // Événements à venir (limités à 6)
        $upcomingEvents = $eventRepository->createQueryBuilder('e')
            ->where('e.isActive = :active')
            ->andWhere('e.eventDate > :now')
            ->setParameter('active', true)
            ->setParameter('now', new \DateTime())
            ->orderBy('e.eventDate', 'ASC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        return $this->render('home/index.html.twig', [
            'stats' => $stats,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }
}
