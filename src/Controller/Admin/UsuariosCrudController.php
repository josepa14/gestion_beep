<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Usuarios;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UsuariosCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Usuarios::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id'),
            TextField::new('username'),
            TextField::new('nombre'),
            TextField::new('email'),
            TextField::new('telefono'),
            ArrayField::new('roles'),
            BooleanField::new('isVerified'),
        ];
    }
    
}
