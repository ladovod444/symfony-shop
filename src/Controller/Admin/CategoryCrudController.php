<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureActions(\EasyCorp\Bundle\EasyAdminBundle\Config\Actions $actions): Actions
    {

        return parent::configureActions($actions)
          ->add(Crud::PAGE_INDEX, Action::DETAIL)
          //->add(Crud::PAGE_INDEX, $impersonate)
          ;
    }


    public function configureFields(string $pageName): iterable
    {
//        yield IntegerField::new('id');
        $products = CollectionField::new('products')
          //->renderExpanded()
//          ->useEntryCrudForm(ProductCrudController::class)
//          ->setEntryIsComplex()
        ;


        if (Crud::PAGE_EDIT !== $pageName && Crud::PAGE_NEW !== $pageName) {

            yield IntegerField::new('id');
            yield $products;
        }
        yield TextField::new('title');
        yield TextField::new('slug');

    }

}
