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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;  // Correct import for EntityType
use Symfony\Component\Validator\Constraints as Assert;

class RecompenseFideliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => true,
            ])
            ->add('points_requis', IntegerType::class, [
                'label' => 'Points requis',
                'required' => true,
                'attr' => ['min' => 1],  // Optional: Add minimum value for points
            ])
            ->add('date_expiration', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'label' => 'Date d\'expiration',
                'required' => true,
            ])
            // Use EntityType to dynamically fetch the available types
            ->add('typeRecompense', EntityType::class, [
                'class' => TypeRecompense::class,  // Set the class of the related entity
                'choice_label' => 'nom',  // Display the 'nom' field in the form dropdown
                'label' => 'Type de Récompense',
                'placeholder' => 'Sélectionnez un type',  // Optional: placeholder option
                'required' => true, // Ensure this field is required
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le type de récompense est obligatoire.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecompenseFidelite::class,
        ]);
    }
}
