<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/events')]
class PublicEventController extends AbstractController
{
    #[Route('/public', name: 'event_public')]
    public function publicEvents(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(['isActive' => true], ['eventDate' => 'ASC']);
        
        return $this->render('event/public.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/{id}/details', name: 'event_details')]
    public function eventDetails(Event $event): Response
    {
        return $this->render('event/details.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/show', name: 'event_show_public')]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
} 