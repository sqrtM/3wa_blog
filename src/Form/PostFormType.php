<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('imageFile', VichFileType::class, [
                'label' => 'Image',
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'class' => 'App\Entity\Category', // Replace with the actual namespace of your Category entity
                'choice_label' => 'name', // Property of Category entity to display in the dropdown
                'multiple' => true, // Allow multiple category selection
                'expanded' => true, // Render as checkboxes
                'required' => false, // Allow no category selection
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
