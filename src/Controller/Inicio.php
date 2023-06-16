<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class Inicio extends AbstractController
{
   
    #[Route(path: '/',name: 'index')]
    public function inicio(): Response
    {
        $user = $this->getUser();

        if ($user && !$user->isVerified()) {
            $this->addFlash('auth', 'Tu cuenta aun no esta activada.');
            return $this->redirectToRoute('app_logout');
        }
       
        return $this->render('index.html.twig');
    }
}