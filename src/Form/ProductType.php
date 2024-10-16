<?php

namespace App\Form;

use App\Entity\Conference;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          ->add('author', null, [
              'label' => 'Your name',
          ])
          ->add('text')
          ->add('email', EmailType::class)
          ->add('photoFilename', FileType::class, [
              'required' => false,
              'mapped' => false,
              'constraints' => [
                  new Image(['maxSize' => '1024k']),
              ],
          ]);
        //      ->add('createdAt', null, [
        //        'widget' => 'single_text',
        //      ])
        //      ->add('conference', EntityType::class, [
        //        'class' => Conference::class,
        //        'choice_label' => 'id',
        //      ])->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
