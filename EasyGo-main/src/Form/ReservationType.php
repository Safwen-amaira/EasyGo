<?php
// src/Form/ReservationType.php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre_places', IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'min' => 1,
                    'max' => $options['available_seats'],
                    'class' => 'form-control'
                ]
            ])
            ->add('type_handicap', ChoiceType::class, [
                'label' => 'Type de handicap',
                'required' => false,
                'choices' => [
                    'Aucun' => null,
                    'Mobilité réduite' => 'mobilite_reduite',
                    'Malvoyant' => 'malvoyant',
                    'Malentendant' => 'malentendant',
                    'Autre' => 'autre'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('lieu_escale', TextType::class, [
                'label' => 'Lieu d\'escale souhaité',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'available_seats' => 1
        ]);
    }
}