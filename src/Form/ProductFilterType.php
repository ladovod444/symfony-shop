<?php

namespace App\Form;

use App\Entity\Category;
use App\Filter\ProductFilter;
use App\Repository\CategoryRepository;
use App\Repository\GenreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFilterType extends AbstractType
{
    public function __construct(private CategoryRepository $categoryRepository,
        private GenreRepository $genreRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices_category = $this->categoryRepository->createQueryBuilder('category')->orderBy('category.title', 'DESC');
        $choices_category = $choices_category->getQuery()->getResult();

        $form_category_choices = [];
        foreach ($choices_category as $choice_category) {
            $form_category_choices[$choice_category->getTitle()] = $choice_category->getId();
        }
        //dd($choices);
        $builder->setMethod('GET')
            ->add('title', TextType::class, ['required' => false])
            // SELECT
//            ->add('category', ChoiceType::class, [
//                'choices' => $form_choices,
//                'empty_data' => null,
//                'placeholder' => '-- Выбор категории --', // Добавление "пустой" категории в список
//                'required' => false
//            ])
            // RADIO
            ->add('category', ChoiceType::class, [
                'choices' => $form_category_choices,
                'empty_data' => null,
                'placeholder' => '-- Вce категории --', // Добавление "пустой" категории в список
                'required' => false,
                'expanded' => true,
                'attr' => ['class' => 'categories'],
//                'multiple' => true,
            ]);
//            ->add('categories', EntityType::class, [
//                'class' => Category::class,
//                'query_builder' => function (CategoryRepository $repository) {
//                    return $repository->createQueryBuilder('category')->orderBy('category.title', 'DESC');
//                },
//                'choice_label' => 'title',
//                'required' => false,
//                'empty_data' => null,
//                'placeholder' => '-- Выбор категории --' // Добавление "пустой" категории в список
//            ])

        $choices_genre = $this->genreRepository->createQueryBuilder('genre')->orderBy('genre.title', 'DESC');
        $choices_genre = $choices_genre->getQuery()->getResult();

        $form_genre_choices = [];
        foreach ($choices_genre as $choice_genre) {
            $form_genre_choices[$choice_genre->getTitle()] = $choice_genre->getId();
        }
          $builder->setMethod('GET')
              ->add('genre', ChoiceType::class, [
                  'choices' => $form_genre_choices,
                  'empty_data' => null,
//                  'placeholder' => '-- Вce жанры --', // Добавление "пустой" категории в список
                  'required' => false,
                  'expanded' => true,
                  'attr' => ['class' => 'genres'],
                  'multiple' => true,
              ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductFilter::class,
            'attr' => ['class' => 'product-filter'],
            'csrf_protection' => false,
        ]);
    }
}
