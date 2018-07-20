<?php

namespace App\Form;

use App\Entity\Hashtag;
use App\Entity\Post;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('posted')
            ->add('dateCreated')
            ->add('publishDate',DateTimeType::class,array(
                
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',)))
            ->add('coverImage')

            ->add('CoverImageFile', FileType::class)
            ->add('title')
            ->add('hashtags', EntityType::class, array(
                'class'=>Hashtag::class,
                'attr'=>array(
                    'class'=>'js-example-basic-multiple',
                ),

                'multiple'=>true,

            ))
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
