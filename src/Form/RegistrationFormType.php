<?php
namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-zÀ-ÿ\s]+$/',
                        'message' => 'Nom must contain only letters and spaces.',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-zÀ-ÿ\s]+$/',
                        'message' => 'Prenom must contain only letters and spaces.',
                    ]),
                ],
            ])
            ->add('cin', TextType::class, [
                'label' => 'National ID',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => 'CIN must be exactly 8 digits.',
                    ]),
                ],
            ])
            ->add('PasswordNet', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Passwords must match.',
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password'],
                'attr' => ['autocomplete' => 'new-password']
            ])
            ->add('isRider', CheckboxType::class, ['required' => false])
            ->add('isDriver', CheckboxType::class, ['required' => false])
        


            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $isRider = $form->get('isRider')->getData();
                $isDriver = $form->get('isDriver')->getData();

                if (($isRider && $isDriver) || (!$isRider && !$isDriver)) {
                    // Now FormError will be recognized
                    $form->get('isRider')->addError(new FormError('Please select either Rider or Driver (not both).'));
                }
            });
    }
 
        

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
