<?php

namespace App\Form;

use App\Entity\Recompensefidelite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecompenseFideliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descriptionRecompense')
            ->add('pointsRequis')
            ->add('typeRecompense')
            ->add('dateExpiration', DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('bonusPoints')

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecompenseFidelite::class,
        ]);
    }
}
