<?php

namespace App\Form;

use App\Entity\Trip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('trip_date', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'form-control datepicker',
                    'placeholder' => 'jj/mm/aaaa'
                ]
            ])
            ->add('departure_point')
            ->add('destination')
            ->add('departure_time', TimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => [
                    'class' => 'form-control timepicker',
                    'placeholder' => 'HH:MM'
                ]
            ])
            ->add('return_time', TimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'required' => false,
                'attr' => [
                    'class' => 'form-control timepicker',
                    'placeholder' => 'HH:MM'
                ]
            ])
            ->add('available_seats')
            ->add('trip_type', ChoiceType::class, [
                'choices' => [
                    'Aller simple' => 'aller',
                    'Retour simple' => 'retour',
                    'Aller-Retour' => 'aller_retour'
                ],
                'placeholder' => 'Choisissez un type'
            ])
            ->add('contribution', NumberType::class, [
                'required' => false,
                'html5' => true,
                'scale' => 2,
                'attr' => [
                    'step' => '0.01',
                    'min' => '0'
                ]
            ])
            ->add('relay_points', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Séparez les points par des virgules'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}