<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'Technique' => 'technique',
                    'Coût' => 'cout',
                    'Communication' => 'communication',
                ],
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La catégorie est obligatoire.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La description est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'max' => 1000,
                        'minMessage' => 'La description doit faire au moins {{ limit }} caractères.',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
