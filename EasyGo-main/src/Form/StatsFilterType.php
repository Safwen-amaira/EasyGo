<?php

namespace App\Form;

use App\Entity\Trip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatsFilterType extends AbstractType
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
            ->add('trip_type')
            ->add('contribution')
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
