<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(["ROLE_USER"]);

            $entityManager->persist($user);

            // do anything else you need here, like send an email
            $message = (new TemplatedEmail())
                        ->subject("Création d'un nouveau compte")
                        ->from("from@test.com")
                        ->to(new Address("destinataire1@test.com"))
                        ->addTo(new Address("destinataire2@test.com"))
                        ->addTo(new Address("destinataire3@test.com"))
                        ->htmlTemplate("emails/register.html.twig")
                        ->context([
                            'user' => $user,
                            "mdp" => $form->get('plainPassword')->getData()
                        ])
            ;

            $mailer->send($message);

            $this->addFlash(
                'success_register',
                'Votre compte a été créé, vous allez recevoir un email avec vos identifiants !'
            );

            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
