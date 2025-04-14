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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
<<<<<<< HEAD
{
    $builder
        ->add('nomprenom', TextType::class, [
            'label' => 'Nom et Prénom', // Personnalisation du label
            'attr' => [
                'class' => 'form-control',
                'novalidate' => 'novalidate', // Désactive la validation HTML5
            ],
            'required' => false
        ])
        ->add('adresse', TextType::class, [
            'label' => 'fournisseur_service' // Personnalisation du label
        ])
        ->add('telephone', TextType::class, [
            'label' => 'Numéro de téléphone' // Personnalisation du label
        ])
        ->add('typeContrat', TextType::class, [
            'label' => 'service_inclus' // Personnalisation du label
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
        ->add('description', TextType::class, [
            'label' => 'type_contrat' // Personnalisation du label
        ])
        ->add('vehicule', EntityType::class, [
            'label' => 'Véhicule', // Personnalisation du label
            'class' => Vehicule::class,
            'choice_label' => 'id',
        ]);
}

=======
    {
        $builder
            ->add('nomprenom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'novalidate' => 'novalidate', // Désactive la validation HTML5
                ],
                'required' => false // Important pour éviter required HTML5
            ])
            ->add('adresse')
            ->add('telephone')
            ->add('typeContrat')



            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
            
                
                'empty_data' => null,
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                
                'empty_data' => null,
            ])
            


           
            ->add('description')
            ->add('vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choice_label' => 'id',
            ]);
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
>>>>>>> 2e8a2d4a8ada53e0c16bc283700a8efcda9ba6b2
}
