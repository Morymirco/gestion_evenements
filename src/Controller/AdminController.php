<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Registration;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\EventForm;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryForm;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route(name: 'admin_dashboard')]
    public function index(EventRepository $eventRepo, UserRepository $userRepo, RegistrationRepository $regRepo): Response
    {
        return $this->render('admin/index.html.twig', [
            'events' => $eventRepo->findAll(),
            'users' => $userRepo->findAll(),
            'registrations' => $regRepo->findAll(),
        ]);
    }

    #[Route('/events', name: 'admin_events')]
    public function events(EventRepository $eventRepo): Response
    {
        return $this->render('admin/events.html.twig', [
            'events' => $eventRepo->findAll(),
        ]);
    }

    #[Route('/users', name: 'admin_users')]
    public function users(UserRepository $userRepo): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepo->findAll(),
        ]);
    }

    #[Route('/registrations', name: 'admin_registrations')]
    public function registrations(RegistrationRepository $regRepo): Response
    {
        return $this->render('admin/registrations.html.twig', [
            'registrations' => $regRepo->findAll(),
        ]);
    }

    #[Route('/events/new', name: 'admin_event_new')]
    public function newEvent(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventForm::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($event);
            $this->entityManager->flush();
            $this->addFlash('success', 'Événement créé avec succès.');
            return $this->redirectToRoute('admin_events');
        }

        return $this->render('admin/event_form.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'is_edit' => false,
        ]);
    }

    #[Route('/events/{id}/edit', name: 'admin_event_edit')]
    public function editEvent(Event $event, Request $request): Response
    {
        $form = $this->createForm(EventForm::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Événement modifié avec succès.');
            return $this->redirectToRoute('admin_events');
        }

        return $this->render('admin/event_form.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'is_edit' => true,
        ]);
    }

    #[Route('/events/{id}/delete', name: 'admin_event_delete', methods: ['POST'])]
    public function deleteEvent(Event $event, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete_event_'.$event->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($event);
            $this->entityManager->flush();
            $this->addFlash('success', 'Événement supprimé avec succès.');
        }
        return $this->redirectToRoute('admin_events');
    }

    #[Route('/categories', name: 'admin_categories')]
    public function categories(): Response
    {
        $categories = $this->entityManager->getRepository(\App\Entity\Category::class)->findAll();
        return $this->render('admin/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/new', name: 'admin_category_new')]
    public function newCategory(Request $request): Response
    {
        $category = new \App\Entity\Category();
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            $this->addFlash('success', 'Catégorie créée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category_form.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'is_edit' => false,
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'admin_category_edit')]
    public function editCategory(\App\Entity\Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Catégorie modifiée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category_form.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'is_edit' => true,
        ]);
    }

    #[Route('/categories/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function deleteCategory(\App\Entity\Category $category, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete_category_'.$category->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();
            $this->addFlash('success', 'Catégorie supprimée avec succès.');
        }
        return $this->redirectToRoute('admin_categories');
    }
} 