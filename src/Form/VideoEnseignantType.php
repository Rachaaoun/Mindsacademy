<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Video;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoEnseignantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('titre',TextType::class,[
            'attr' => ['class' => 'form-control' , 'placeholder' => 'Titre',
                ]
        ])
        ->add('image',FileType::class,[
            'data_class' => null,
            'attr' => ['class' => 'form-control' , 'placeholder' => '',
                ]
        ])
        ->add('lienvideo',TextType::class,[
            'attr' => ['class' => 'form-control' , 'placeholder' => 'lienvideo',
                ]
        ])
        ->add('cours',EntityType::class,[
            'class'=>Cours::class,
            'attr' => ['class' => 'form-control' , 'placeholder' => 'Titre',
                ]
        ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
