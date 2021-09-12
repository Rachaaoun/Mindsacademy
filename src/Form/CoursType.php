<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Enseignant;
use App\Entity\Matiere;
use App\Entity\Niveau;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType as TypeIntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'titre',
                    ]
            ])
            ->add('image',FileType::class,[
                'data_class' => null,
                'attr' => ['class' => 'form-control' 
                    ]
            ])
            ->add('description',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'description',
                    ]
            ])
            ->add('gratuit')
            ->add('frais',TextType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'Frais',
                    ]
            ])
            ->add('placedisponible',TypeIntegerType::class,[
                'attr' => ['class' => 'form-control' , 'placeholder' => 'Place Disponible',
                    ]
            ])
 
            ->add('enseignant',EntityType::class,[
                'class'=>Enseignant::class,
                'attr' => ['class' => 'form-control' 
                    ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
