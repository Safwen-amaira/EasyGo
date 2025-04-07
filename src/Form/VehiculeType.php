<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => 'novalidate', // Désactive la validation HTML5
                ],
                'required' => false // Important pour éviter required HTML5
            ])

          

            ->add('updated', DateType::class, [
                'widget' => 'single_text',
            
                
                'empty_data' => null,
            ])
            ->add('created', DateType::class, [
                'widget' => 'single_text',
                
                'empty_data' => null,
            ])
            
           
            ->add('image', FileType::class, [
                'label' => 'Image du véhicule',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('content', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => 'novalidate',
                ],
                'required' => false
            ])
            ->add('color', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => 'novalidate',
                ],
                'required' => false
            ])
            ->add('prix', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => 'novalidate',
                ],
                'required' => false
            ])
            ->add('totalEnStock', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => 'novalidate',
                ],
                'required' => false
            ])
            ->add('categoriesId', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => 'novalidate',
                ],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
            'attr' => [
                'novalidate' => 'novalidate' // Désactive la validation HTML5 pour tout le formulaire
            ]
        ]);
    }
}