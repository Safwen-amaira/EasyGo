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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Choice;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre_places', IntegerType::class, [
                'label' => 'Nombre de places',
                'constraints' => [
                    new NotBlank(['message' => 'Le nombre de places est obligatoire']),
                    new Positive(['message' => 'Le nombre de places doit être supérieur à 0']),
                    new LessThanOrEqual([
                        'value' => $options['available_seats'],
                        'message' => 'Vous ne pouvez pas réserver plus de {{ compared_value }} places'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => 'novalidate'
                ]
            ])
 // src/Form/ReservationType.php

->add('type_handicap', ChoiceType::class, [
    'label' => 'Type de handicap',
    'placeholder' => 'Sélectionnez un type de handicap',
    'choices' => [
        'Mobilité réduite' => 'mobilite_reduite',
        'Malvoyant' => 'malvoyant',
        'Malentendant' => 'malentendant',
        'Autre' => 'autre'
    ],
    'constraints' => [
        new NotBlank([
            'message' => 'Veuillez sélectionner un type de handicap'
        ]),
        new Choice([
            'choices' => ['mobilite_reduite', 'malvoyant', 'malentendant', 'autre'],
            'message' => 'Veuillez choisir un type de handicap valide'
        ])
    ],
    'attr' => [
        'class' => 'form-select',
        'novalidate' => 'novalidate'
    ]
])
            ->add('lieu_escale', TextType::class, [
                'label' => 'Lieu d\'escale souhaité',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => 'novalidate'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'available_seats' => 1,
            'validation_groups' => ['Default']
        ]);
    }
}