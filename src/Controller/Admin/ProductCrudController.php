<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id');
        yield TextField::new('title');
        yield TextField::new('sku');
        yield TextField::new('current_price');
        yield TextField::new('regular_price');
        yield TextEditorField::new('description');
        yield ImageField::new('image')
          ->setBasePath('/uploads/images')
          ->setUploadDir('public/uploads/images')
          ->setLabel('Image');
        yield AssociationField::new('category')->autocomplete();
        // ->onlyOnIndex();
        // https://symfony.com/doc/4.x/EasyAdminBundle/fields/AssociationField.html
//        yield AssociationField::new('user_id')
//          ->setQueryBuilder(fn () => $this->userRepository->createQueryBuilder('user')
////            ->where('entity.some_property = :some_value')
////            ->setParameter('some_value', '...')
//              ->orderBy('user.email', 'ASC')
//          ); // ->renderAsHtml();
        $createdAt = DateTimeField::new('createdAt')->setFormTypeOptions([
            'years' => range((int) date('Y'), (int) date('Y') + 5),
            'widget' => 'single_text',
        ]);
        if (Crud::PAGE_EDIT === $pageName) {
            // yield $createdAt->setFormTypeOption('disabled', true);
            //    } else {
            //      yield $createdAt;
        }
    }

    public function configureActions(\EasyCorp\Bundle\EasyAdminBundle\Config\Actions $actions): Actions
    {

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            //->add(Crud::PAGE_INDEX, $impersonate)
            ;
    }
}
