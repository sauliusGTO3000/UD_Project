<?php
/**
 * Created by PhpStorm.
 * User: SauliusGTO3000
 * Date: 7/16/2018
 * Time: 08:41
 */

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class AuthorFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('birthday')
            ->add('username')
            ->add('email')
            ->add('profilePicture')
            ->add('profilePictureFile', FileType::class,array(
                'required'=>false,
            ))
            ->add('bio')

        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}