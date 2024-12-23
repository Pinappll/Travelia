<?php

namespace App\Form;

use App\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du pays',
                'attr' => [
                    'placeholder' => 'Entrez le nom du pays',
                    'class' => 'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du pays ne peut pas être vide.']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom du pays ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('code', TextType::class, [
                'label' => 'Code du pays',
                'attr' => [
                    'placeholder' => 'Entrez le code du pays (ex : FR, US)',
                    'class' => 'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le code du pays ne peut pas être vide.']),
                    new Length([
                        'max' => 10,
                        'maxMessage' => 'Le code du pays ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du pays',
                'mapped' => false, // Si le fichier n'est pas stocké directement dans l'entité
                'required' => false,
                'attr' => [
                    'class' => 'w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier valide (JPEG, PNG, GIF).',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}
