<?php

namespace App\Controller\Admin;

use App\Entity\Notas;
use App\Entity\Productos;
use App\Entity\Usuarios;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        if (in_array("ROLE_ADMIN",$this->getUser()->getRoles())) {
            return $this->render('admin/index.html.twig');
            }         
            return $this->redirect('/');
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Gestion Beep');
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Gestionar Usuarios', 'fas fa-list', Usuarios::class);
        yield MenuItem::linkToRoute('Volver a la página principal', 'fa fa-home', 'index');
        // yield MenuItem::linkToCrud('Crud Productos', 'fas fa-list', Productos::class);
        // yield MenuItem::linkToCrud('Crud Notas', 'fas fa-list', Notas::class);

    }
}
