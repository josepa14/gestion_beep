<?php

namespace App\Form;

use App\Entity\Usuarios;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setAttributes([
                'attr' => [
                    'id' => 'registro',
                ],
            ])
            ->add('username', TextType::class, [
                'attr' => array('class' => 'form-control form-control-lg ', 'placeholder' => 'Ingrese su nombre de usuaria',
                ),
                'label' => 'Usuario',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El campo usuario no puede estar vacio'
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'El usuario debe tener almenos {{ limit }} letras',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('nombreCompleto', TextType::class, [
                'attr' => array('class' => 'form-control form-control-lg', 'placeholder' => 'Ingrese su nombre completo',
                ),
                'label' => 'Nombre Completo',
                'constraints' => [
                    new NotBlank([
                        'message' => 'El campo nombre no puede estar vacio'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'El nombre debe tener almenos {{ limit }} letras',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control form-control-lg', 'placeholder' => 'Ingrese un correo', )))
            ->add('telefono', TelType::class, [
                'label' => 'Teléfono',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Ingrese su número de teléfono',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'El campo teléfono no puede estar vacío',
                    ]),
                    // Restricciones adicionales para el teléfono si las deseas...
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Las contraseñas no coinciden.',
                'options' => ['attr' => ['class' => 'password-field form-control form-control-lg']],
                'required' => true,
                'first_options' => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Repetir contraseña'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, ingresa una contraseña.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'La contraseña debe tener al menos {{ limit }} caracteres.',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{6,}$/
                        ',
                        'message' => 'La contraseña debe contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.',
                    ]),

                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuarios::class,
        ]);
    }
}