<?php

namespace App\Form;

use App\Entity\TypeRecompense;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TypeRecompenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z\s]+$/',
                        'message' => 'Le nom ne doit contenir que des lettres.',
                    ])
                ],
                'label' => 'Nom',
            ])
            ->add('categorie', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'La catégorie ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'label' => 'Catégorie',
            ])
            ->add('actif', CheckboxType::class, [
                'required' => false,
                'label' => 'Actif',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypeRecompense::class,
        ]);
    }
}
