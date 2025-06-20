<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Registration;
use App\Form\RegistrationForm;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[Route('/registration')]
#[IsGranted('ROLE_USER')]
final class RegistrationController extends AbstractController
{
    #[Route('/{id}/confirm', name: 'registration_confirm')]
    #[IsGranted('ROLE_ADMIN')] // Seul un admin peut confirmer
    public function confirmRegistration(Registration $registration, EntityManagerInterface $entityManager): Response
    {
        $registration->setIsConfirmed(true);
        $entityManager->flush();

        $this->addFlash('success', 'Inscription confirmée avec succès.');

        return $this->redirectToRoute('registration_index');
    }
    #[Route(name: 'app_registration_index', methods: ['GET'])]
    public function index(RegistrationRepository $registrationRepository): Response
    {
        return $this->render('registration/index.html.twig', [
            'registrations' => $registrationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_registration_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $registration = new Registration();
        $form = $this->createForm(RegistrationForm::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($registration);
            $entityManager->flush();

            return $this->redirectToRoute('app_registration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registration/new.html.twig', [
            'registration' => $registration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_registration_show', methods: ['GET'])]
    public function show(Registration $registration): Response
    {
        return $this->render('registration/show.html.twig', [
            'registration' => $registration,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_registration_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Registration $registration, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RegistrationForm::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_registration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('registration/edit.html.twig', [
            'registration' => $registration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_registration_delete', methods: ['POST'])]
    public function delete(Request $request, Registration $registration, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$registration->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($registration);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_registration_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/event/{id}/register', name: 'event_register')]
    #[IsGranted('ROLE_USER')]
    public function register(Event $event, EntityManagerInterface $em)
    {
        $user = $this->getUser();

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

            $this->addFlash('success', 'Inscription effectuée. En attente de confirmation.');
        }

        return $this->redirectToRoute('event_index');
    }

    #[Route('/event/{id}/unregister', name: 'event_unregister')]
    #[IsGranted('ROLE_USER')]
    public function unregister(Event $event, EntityManagerInterface $em)
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
            $this->addFlash('warning', 'Vous n’êtes pas inscrit à cet événement.');
        }

        return $this->redirectToRoute('event_index');
    }
}

