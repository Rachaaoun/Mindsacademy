<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'Nom',
                    ]
            ])
            ->add('prenom',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'Prenom',
                    ]
            ])
            ->add('datedenaissance',BirthdayType::class,[
                'attr' => ['class' => 'form-control' ,   
                    ]
            ])
            ->add('lieudenaissance',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'Lieu de naissance ',
                    ]
            ])
            ->add('classe',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'Votre Classe',
                    ]
            ])
            ->add('email',EmailType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'Email',
                    ]
            ])
            ->add('image',FileType::class,[
                'data_class' => null,
                'attr' => ['class' => 'form-control' 
                    ]
            ])
            ->add('numerotelephone',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'Numero telephone',
                    ]
            ])
            ->add('plainPassword', PasswordType::class, [
                
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password',
                'class' => 'form-control' , 'placeholder' => 'Mot de passe'
            ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('accepterlesconditions', CheckboxType::class, [
                
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
