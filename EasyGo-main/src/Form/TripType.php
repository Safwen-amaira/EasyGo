<?php

namespace App\Form;

use App\Entity\Trip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Cette ligne manquait
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('trip_date', null, [
                'widget' => 'single_text',
            ])
            ->add('departure_point')
            ->add('destination')
            ->add('departure_time', null, [
                'widget' => 'single_text',
            ])
            ->add('return_time', null, [
                'widget' => 'single_text',
            ])
            ->add('available_seats')
            ->add('trip_type', ChoiceType::class, [ 
                'label' => 'Type de trajet',
                'choices' => [
                    'Aller simple' => 'aller',
                    'Retour simple' => 'retour',
                    'Aller-Retour' => 'aller_retour'
                ],
                'placeholder' => 'Choisissez un type',
                'attr' => [
                    'class' => 'form-select border-start-0'
                ],
                'label_attr' => [
                    'class' => 'form-label fw-bold text-dark'
                ]
            ])
            ->add('contribution', NumberType::class, [
                'required' => false,
                'html5' => true,
                'scale' => 2, 
                'attr' => [
                    'step' => '0.01' 
                ]
            ])            
            ->add('relay_points')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}