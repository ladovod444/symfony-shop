<?php

namespace App\Controller\Admin;

use App\Controller\OrderItemController;
use App\Entity\Order;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id');
        yield AssociationField::new('owner')
            ->setQueryBuilder(fn() => $this->userRepository->createQueryBuilder('user')
//            ->where('entity.some_property = :some_value')
//            ->setParameter('some_value', '...')
                ->orderBy('user.email', 'ASC')
            ); // ->renderAsHtml();
        $createdAt = DateTimeField::new('createdAt')->setFormTypeOptions([
            'years' => range((int)date('Y'), (int)date('Y') + 5),
            'widget' => 'single_text',
        ]);
        $updatedAt = DateTimeField::new('updatedAt')->setFormTypeOptions([
            'years' => range((int)date('Y'), (int)date('Y') + 5),
            'widget' => 'single_text',
        ]);

        $status = TextField::new('status');

        $orderItems = CollectionField::new('orderItems')
            ->useEntryCrudForm(OrderItemCrudController::class)
            ->setEntryIsComplex();

        if (Crud::PAGE_EDIT === $pageName) {
            yield $createdAt->setFormTypeOption('disabled', true);
            yield $updatedAt->setFormTypeOption('disabled', true);

            yield $orderItems;
            yield $status;
        } else {
            yield $createdAt;
            yield $updatedAt;
            yield $status;
        }

    }

    public function configureActions(\EasyCorp\Bundle\EasyAdminBundle\Config\Actions $actions): Actions
    {

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

}
