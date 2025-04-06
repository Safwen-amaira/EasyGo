<?php

namespace App\Form;

use App\Entity\Utilisateurfidelite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurFideliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomUtilisateur')
            ->add('pointsAccumules')
            ->add('totalTrajetsEffectues')
            ->add('totalMontantDepense')
            ->add('dateDerniereMiseAJour', DateTimeType::class, [
                'widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurfidelite::class,
        ]);
    }
}
