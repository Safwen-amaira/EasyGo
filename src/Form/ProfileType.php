<?php

namespace App\Form;

use App\Entity\Profiles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]+$/u',
                        'message' => 'Name can only contain letters, spaces, and hyphens'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]+$/u',
                        'message' => 'Name can only contain letters, spaces, and hyphens'
                    ])
                ]
            ])
            ->add('phone', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 8, 'max' => 8]),
                    new Assert\Regex(['pattern' => '/^\d+$/'])
                ]
            ])
            ->add('bio', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 500])
                ]
            ])
            ->add('requirements', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 1000])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profiles::class,
            'csrf_protection' => false
        ]);
    }
}