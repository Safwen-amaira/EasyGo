<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomprenom', TextType::class, [
                'label' => 'Nom', // Personnalisation du label
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => 'novalidate', // Désactive la validation HTML5
                ],
                'required' => false
            ])

            ->add('adresse', TextType::class, [
                'label' => 'Fournisseur de service' // Personnalisation du label
            ])


            ->add('telephone', TextType::class, [
                'label' => 'Numéro de téléphone' // Personnalisation du label
            ])


            ->add('typeContrat',  TextType::class, [
               'label' => 'service_inclus'
            ])


            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début', // Personnalisation du label
                'widget' => 'single_text',
                'empty_data' => null,
            ])


            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin', // Personnalisation du label
                'widget' => 'single_text',
                'empty_data' => null,
            ])
            
            ->add('description',ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => [
                    'Annuel' => 'annuel',
                    'Semestriel' => 'semestriel',
                    'Trimestriel' => 'trimestriel',
                    'Kilométrique' => 'kilométrique'
                ],
                'placeholder' => 'Sélectionnez un type de contrat',
                'required' => true, // ou false selon vos besoins
                'attr' => [
                    'class' => 'form-control' // classe CSS optionnelle
                ]
            ])


            
            ->add('vehicule', EntityType::class, [
                'label' => 'Véhicule', // Personnalisation du label
                'class' => Vehicule::class,
                'choice_label' => 'id',
            ]);
    }
}
