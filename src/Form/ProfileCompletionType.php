<?php
// src/Form/GoogleProfileCompletionType.php
namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProfileCompletionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ])
            ->add('cin', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 8, 'max' => 8]),
                    new Assert\Regex(['pattern' => '/^\d+$/'])
                ]
            ])
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2])
                ]
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2])
                ]
            ])
            ->add('isRider', CheckboxType::class, [
                'required' => false,
                'label' => 'I am a Rider'
            ])
            ->add('isDriver', CheckboxType::class, [
                'required' => false,
                'label' => 'I am a Driver'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
            'validation_groups' => ['google_completion']
        ]);
    }

    public function validateUserType($value, ExecutionContextInterface $context): void
    {
        $user = $context->getObject();
        if (!$user->isRider() && !$user->isDriver()) {
            $context->buildViolation('You must select at least one role (Rider or Driver)')
                    ->atPath('isRider')
                    ->addViolation();
        }
    }
}