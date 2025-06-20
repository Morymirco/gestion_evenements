<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/registration')]
#[IsGranted('ROLE_USER')]
class PublicRegistrationController extends AbstractController
{
    #[Route('/event/{id}/register', name: 'event_register')]
    public function register(Event $event, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Vérifier si l'événement est actif et futur
        if (!$event->isActive()) {
            $this->addFlash('warning', 'Cet événement n\'est pas actif.');
            return $this->redirectToRoute('event_details', ['id' => $event->getId()]);
        }

        if ($event->getEventDate() < new \DateTime()) {
            $this->addFlash('warning', 'Cet événement est déjà terminé.');
            return $this->redirectToRoute('event_details', ['id' => $event->getId()]);
        }

        // Vérifier si l'utilisateur est déjà inscrit
        $existingRegistration = $em->getRepository(Registration::class)->findOneBy([
            'user' => $user,
            'event' => $event,
        ]);

        if ($existingRegistration) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cet événement.');
        } else {
            $registration = new Registration();
            $registration->setUser($user);
            $registration->setEvent($event);
            $registration->setRegisteredAt(new \DateTimeImmutable());
            $registration->setIsConfirmed(false); // En attente de confirmation par admin

            $em->persist($registration);
            $em->flush();

            $this->addFlash('success', 'Inscription effectuée avec succès ! En attente de confirmation par l\'administrateur.');
        }

        return $this->redirectToRoute('event_details', ['id' => $event->getId()]);
    }

    #[Route('/event/{id}/unregister', name: 'event_unregister')]
    public function unregister(Event $event, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $registration = $em->getRepository(Registration::class)->findOneBy([
            'user' => $user,
            'event' => $event,
        ]);

        if ($registration) {
            $em->remove($registration);
            $em->flush();
            $this->addFlash('success', 'Vous êtes désinscrit avec succès.');
        } else {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cet événement.');
        }

        return $this->redirectToRoute('event_details', ['id' => $event->getId()]);
    }
} 