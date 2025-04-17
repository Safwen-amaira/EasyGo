<?php

namespace App\Form;

use App\Entity\Trip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank; 
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; 
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('trip_date', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Callback([$this, 'validateDate']),
                ],
            ])
            ->add('departure_point')
            ->add('destination')
            ->add('departure_time', TimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Callback([$this, 'validateDepartureTime']),
                ],
            ])
            ->add('return_time', TimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Callback([$this, 'validateReturnTime']),
                ],
            ])
            ->add('available_seats')
            ->add('trip_type', ChoiceType::class, [
                'choices' => [
                    'Aller simple' => 'aller',
                    'Retour simple' => 'retour',
                    'Aller-Retour' => 'aller_retour'
                ],
                'placeholder' => 'Choisissez un type',
                'label' => 'Type de trajet <span class="text-danger">*</span>',
                'label_html' => true,
                'attr' => [
                    'class' => 'form-select border-start-0'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le type de trajet est obligatoire']),
                ],
            ])           
             ->add('contribution', null, [
                'required' => false,
            ])
            ->add('relay_points', null, [
                'required' => false,
            ]);
    }

    public function validateDate($value, ExecutionContextInterface $context)
    {
        if (!$value) {
            return;
        }

        $today = new \DateTime();
        $oneWeekLater = (new \DateTime())->modify('+1 week');

        if ($value < $today || $value > $oneWeekLater) {
            $context->buildViolation('La date doit être entre aujourd\'hui et dans une semaine')
                ->atPath('trip_date')
                ->addViolation();
        }
    }

    public function validateDepartureTime($value, ExecutionContextInterface $context)
    {
        if (!$value) {
            return;
        }

        $hour = (int)$value->format('H');
        if ($hour < 8 || $hour > 18) {
            $context->buildViolation('L\'heure de départ doit être entre 8h et 18h')
                ->atPath('departure_time')
                ->addViolation();
        }
    }

    public function validateReturnTime($value, ExecutionContextInterface $context)
    {
        if (!$value) {
            return;
        }

        $hour = (int)$value->format('H');
        if ($hour < 8 || $hour > 18) {
            $context->buildViolation('L\'heure de retour doit être entre 8h et 18h')
                ->atPath('return_time')
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}