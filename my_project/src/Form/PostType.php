<?php

namespace App\Form;

use App\Entity\Post;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('posted')
            ->add('dateCreated')
            ->add('publishDate')

            ->add('coverImage')
            ->add('title')
            ->add('hashtags')
            ->add('readCount')
            ->add('content', CKEditorType::class, array(
                'config'=>array(
                    'filebrowserUploadRoute'=>'uploadImage',

                )))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
