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
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le nombre de points requis ne peut pas être négatif.',
                    ]),
                ],
            ])
            ->add('date_expiration', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date d\'expiration',
                'required' => true,
                'constraints' => [
                    new Assert\GreaterThan([
                        'value' => "today",
                        'message' => 'La date d\'expiration doit être dans le futur.',
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
                    new Assert\NotBlank(['message' => 'Le type de récompense est obligatoire.']),
                ],
            ]);

        // Add the event listener to validate the 'typeRecompense' field
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $recompenseFidelite = $form->getData();
            $typeRecompense = $recompenseFidelite->getTypeRecompense();

            // Check if the selected TypeRecompense is active
            if ($typeRecompense && !$typeRecompense->isActif()) {
                // If the type is inactive, add a custom error using FormError
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
