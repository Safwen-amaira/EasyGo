<?php

namespace App\Form;

use App\Entity\RecompenseFidelite;
use App\Entity\TypeRecompense;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class RecompenseFideliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est obligatoire.']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?![0-9]+$)[a-zA-Z0-9\s]+$/',
                        'message' => 'La description doit contenir uniquement des lettres et des chiffres, et ne peut pas être composée uniquement de chiffres.',
                    ]),
                ],
            ])
            ->add('points_requis', IntegerType::class, [
                'label' => 'Points requis',
                'required' => true,
                'attr' => ['min' => 0],
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le nombre de points est obligatoire.']),
                    new Assert\Type([
                        'type' => 'integer',
                        'message' => 'Le nombre de points doit être un entier valide.',
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le nombre de points requis ne peut pas être négatif.',
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 100000,
                        'message' => 'Le nombre de points doit être inférieur ou égal à {{ compared_value }}.',
                    ]),
                ],
            ])
            ->add('date_expiration', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date d\'expiration',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date d\'expiration est obligatoire.']),
                    new Assert\GreaterThan([
                        'value' => 'today',
                        'message' => 'La date d\'expiration doit être postérieure à aujourd\'hui.',
                    ]),
                ],
            ])
            ->add('typeRecompense', EntityType::class, [
                'class' => TypeRecompense::class,
                'choice_label' => 'nom',
                'label' => 'Type de Récompense',
                'placeholder' => 'Sélectionnez un type',
                'required' => true,
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le type de récompense est obligatoire.']),
                ],
            ]);

        // ✅ Vérification personnalisée après soumission
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $recompenseFidelite = $form->getData();
            $typeRecompense = $recompenseFidelite->getTypeRecompense();

            if ($typeRecompense && !$typeRecompense->isActif()) {
                $form->get('typeRecompense')->addError(
                    new FormError('Le type de récompense sélectionné est inactif.')
                );
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecompenseFidelite::class,
        ]);
    }
}
