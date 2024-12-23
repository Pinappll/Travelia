<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Trip;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Comment1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Écrivez votre commentaire ici...',
                    'rows' => 5,
                    'class' => 'form-control',
                ],
            ])
            ->add('createdAt', null, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'disabled' => true, // Champ non modifiable
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('publisher', EntityType::class, [
                'class' => User::class,
                'label' => 'Auteur',
                'choice_label' => 'email', // Affiche l'email de l'utilisateur
                'disabled' => true, // Champ non modifiable
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('trip', EntityType::class, [
                'class' => Trip::class,
                'label' => 'Voyage associé',
                'choice_label' => 'title', // Affiche le titre du voyage
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
