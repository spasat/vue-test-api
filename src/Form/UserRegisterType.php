<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Required;

class UserRegisterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, ['constraints' => [new Required(), new Email()]])
            ->add('firstName', TextType::class, ['constraints' => [new Required()]])
            ->add('lastName', TextType::class, ['constraints' => [new Required()]])
            ->add('password', TextType::class, ['constraints' => [new Required()]])
            ->add(
                'passwordConfirmed',
                TextType::class,
                [
                    'constraints' => [
                        new Required(),
                        new Length(['min' => 3])
                    ],
                    'mapped' => false
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'csrf_protection' => false,
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
