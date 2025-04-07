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
}
