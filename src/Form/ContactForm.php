<?php

namespace App\Form;

use App\Entity\Contact;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ContactForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'attr' => [
                    'placeholder' => 'Veuillez écrire votre prénom',
                    'class' => 'form-control'
                ],
                'help' => 'Votre prénom doit être écrit en majuscule'
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'placeholder' => 'Veuillez écrire votre nom',
                    'class' => 'form-control'
                ],
                'help' => 'Votre nom doit être écrit en majuscule'
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Veuillez écrire votre adresse email',
                    'class' => 'form-control'
                ],
                'help' => 'Votre prénom doit être écrit en minuscule'
            ])
            ->add('subject', TextType::class, [
                'attr' => [
                    'placeholder' => 'Veuillez écrire un objet',
                    'class' => 'form-control'
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8
                ]
            ])
            ->add('phone', TelType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('az842546qd4qz', TextType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'style' => 'display:none',
                    'autocomplete' => 'off',
                    'tabindex' => -1
                ]
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'homepage'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
