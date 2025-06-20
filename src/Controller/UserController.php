<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Registration;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route(name: 'user_profile')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/registrations', name: 'user_registrations')]
    public function registrations(RegistrationRepository $registrationRepo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $registrations = $registrationRepo->findBy(['user' => $user], ['registeredAt' => 'DESC']);
        
        return $this->render('user/registrations.html.twig', [
            'registrations' => $registrations,
        ]);
    }

    #[Route('/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $currentPassword = $request->request->get('current_password');
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');
            
            // Validation basique
            if ($email !== $user->getEmail()) {
                $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    $this->addFlash('error', 'Cet email est déjà utilisé.');
                    return $this->redirectToRoute('user_edit');
                }
                $user->setEmail($email);
            }
            
            // Changement de mot de passe
            if (!empty($newPassword)) {
                if (empty($currentPassword)) {
                    $this->addFlash('error', 'Le mot de passe actuel est requis.');
                    return $this->redirectToRoute('user_edit');
                }
                
                if (!$this->isPasswordValid($user, $currentPassword)) {
                    $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                    return $this->redirectToRoute('user_edit');
                }
                
                if ($newPassword !== $confirmPassword) {
                    $this->addFlash('error', 'Les nouveaux mots de passe ne correspondent pas.');
                    return $this->redirectToRoute('user_edit');
                }
                
                $hashedPassword = $this->container->get('security.user_password_hasher')->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }
            
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès.');
            
            return $this->redirectToRoute('user_profile');
        }
        
        return $this->render('user/edit.html.twig', [
            'user' => $user,
        ]);
    }

    private function isPasswordValid(User $user, string $password): bool
    {
        return $this->container->get('security.user_password_hasher')->isPasswordValid($user, $password);
    }
} 