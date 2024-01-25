<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nickname', TextType::class,[
                'label' => 'pseudo',
                'attr' => [
                    'placeholder' => 'rafgenius'
                ],
                'required' => true,
                'constraints' => [
                    new Length(
                        [
                        'min' => 3, 
                        'max' => 10,
                        "minMessage" => 'Pas moins de {{ limit }} caractères', 
                        "maxMessage" => 'Pas plus de {{ limit }} caractères'
                        ]
                        )]
            ])
            ->add('email', TextType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'constraints' => [
                    new Length(
                        [
                        'min' => 2,
                        "minMessage" => 'Pas moins de {{ limit }} caractères'
                        ]
                        )],
                        // new Regex("^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$")]],
                'first_options' => [
                    'label' => "Mot de passe"
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe'
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre'
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Valider'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
