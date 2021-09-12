<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => '',
                    ]
            ])
            ->add('image',FileType::class,[
                'data_class' => null,
                'attr' => ['class' => 'form-control' 
                    ]
            ])
            ->add('description',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => '',
                    ]
            ])
            ->add('date',DateType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => '',
                    ]
            ])
            ->add('heure',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => '',
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
