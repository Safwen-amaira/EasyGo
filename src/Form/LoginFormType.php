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

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback([
                        'callback' => [$this, 'validateUsernameFormat']
                    ])
                ]
            ])
            ->add('password', PasswordType::class);
    }

    public function validateUsernameFormat($value, ExecutionContextInterface $context): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !ctype_digit($value)) {
            $context->buildViolation('Invalid username format - must be email or numeric CIN')
                    ->atPath('username')
                    ->addViolation();
        }
    }
}