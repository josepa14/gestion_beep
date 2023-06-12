<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nombre', TextType::class, [
            'label' => 'Nombre y apellidos',
            'required' => true,
            'attr' => [
                'class' => 'form-control',
            ],
        ])
        ->add('telefono', TextType::class, [
            'required' => true,
            'attr' => [
                'class' => 'form-control',
            ],
        ])
        ->add('asunto', ChoiceType::class, [
            'required' => true,
            'choices' => [
                'Servicio técnico a domicilio' => 'servicio_tecnico',
                'Dudas y preguntas' => 'dudas_preguntas',
                'Otros' => 'otros',
            ],
            'attr' => [
                'class' => 'form-select',
            ],
        ])
        ->add('descripcion', TextareaType::class, [
            'required' => true,
            'attr' => [
                'class' => 'form-control',
                'rows' => 4,
            ],
        ])
        ->add('enviar', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-primary btn-block mt-4 mb-4',
            ],
        ]);
}

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configuración adicional, si es necesario
        ]);
    }
}