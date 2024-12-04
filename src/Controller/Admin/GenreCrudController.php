<?php

namespace App\Controller\Admin;

use App\Entity\Genre;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GenreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Genre::class;
    }


    public function configureFields(string $pageName): iterable
    {
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
