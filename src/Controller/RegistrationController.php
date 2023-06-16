<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Form\RegistrationFormType;
use App\Repository\UsuariosRepository;
use App\Security\AppLoginFormAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/registro', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppLoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Usuarios();
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

            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();
            $user->setIsVerified(false);

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('jcasper2112@g.educaand.es', 'Alvaro Montoro'))
                    ->to($user->getEmail())
                    ->subject('Porfavor confirma tu registro')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email
            $this->addFlash('success2', 'Te has registrado correctamente. Por favor, comprueba tu bandeja de entrada para verificar tu dirección de correo electrónico.');
            return $this->redirectToRoute('index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request,UsuariosRepository $usuariosRepository, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');  
         try {
                $this->emailVerifier->handleEmailConfirmation($request, $this->getUser()); 
            } catch (VerifyEmailExceptionInterface $exception) {
                $this->addFlash('verify_email_error',$exception->getReason());

                return $this->redirectToRoute('registro');
            }

            $this->addFlash('success', 'Tu direccion de email ha sido verificado correctamente.');
 
    
    return $this->redirectToRoute('index');
    }
    
}