<?php

namespace App\Form;

use App\Entity\Productos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', null, [
                'label' => 'Nombre',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('marca', null, [
                'label' => 'Marca',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('categoria', ChoiceType::class, [
                'label' => 'Categoría',
                'choices' => [
                    'Móviles' => 'moviles',
                    'Tablets' => 'tablets',
                    'Ordenadores' => 'ordenadores',
                    'Portátiles' => 'portatiles',
                    'Periféricos' => 'perifericos',
                ],
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Seleccionar categoría',
                'required' => true,
            ])
            ->add('subcategoria', ChoiceType::class, [
                'label' => 'Subcategoría',
                'choices' => [
                    'Auriculares' => 'auriculares',
                    'Baterías' => 'baterias',
                    'Cables' => 'cables',
                    'Carcasas' => 'carcasas',
                    'Cargadores' => 'cargadores',
                    'Discos Duros' => 'discos_duros',
                    'Fuentes de Alimentación' => 'fuentes_alimentacion',
                    'Impresoras' => 'impresoras',
                    'Pantallas' => 'pantallas',
                    'Placas Base' => 'placas_base',
                    'Portátiles' => 'portatiles',
                    'RAM' => 'ram',
                    'Ratones' => 'ratones',
                    'Teclados' => 'teclados',
                    'Torres' => 'torres',
                    'Otros' => 'otros',
                ],
                'attr' => ['class' => 'form-control form-control-lg'],
                'placeholder' => 'Seleccionar subcategoría',
                'required' => true,
            ])
            ->add('stock', null, [
                'label' => 'Stock',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('estado', null, [
                'label' => 'Estado',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('precio', null, [
                'label' => 'Precio',
                'attr' => ['class' => 'form-control form-control-lg']
            ])
            ->add('imagen', FileType::class, [
                'label' => 'Imagen',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'file-input form-control-lg',
                    'data-theme' => 'fas',
                    'data-show-upload' => 'false',
                    'data-show-caption' => 'true',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Productos::class,
        ]);
    }
}
