<?php
namespace App\Controller;

use App\Form\ContactoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactoController extends AbstractController
{
    #[Route('/contacto', name: 'app_contacto')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $asunto = $data['asunto'];
            $dir=$this->getParameter('correo_remitente');
            // Crear el mensaje de correo
            $email = (new Email())
                ->from($dir)
                ->to($dir)
                ->subject($asunto)
                ->html($this->renderView('contacto/confirmacion_email_contacto.html.twig', [
                    'data' => $data,
                ]));

            // Enviar el correo
            $mailer->send($email);

            // Redirigir al index con mensaje de enviado
            $this->addFlash('contacto', 'El formulario se ha enviado correctamente. A lo largo del dia nos pondremos en contacto contigo');

            return $this->redirectToRoute('index');
        }

        return $this->render('contacto/contacto.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
?>