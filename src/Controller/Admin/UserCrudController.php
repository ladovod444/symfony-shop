<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        //return [
        yield EmailField::new('email');
        yield ChoiceField::new('roles')
            ->setChoices([
                'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                'ROLE_ADMIN' => 'ROLE_ADMIN',
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_EDITOR' => 'ROLE_EDITOR',
            ])
            ->allowMultipleChoices()->renderExpanded();
        yield DateTimeField::new('createdAt')->setFormTypeOptions([
            'years' => range((int)date('Y'), (int)date('Y') + 5),
            'widget' => 'single_text',
        ])->setDisabled(); //->hideOnIndex(),
        yield DateTimeField::new('updatedAt')->setFormTypeOptions([
            'years' => range((int)date('Y'), (int)date('Y') + 5),
            'widget' => 'single_text',
        ])->setDisabled();
        //BooleanField::new('enabled')->onlyOnIndex(),
        yield BooleanField::new('enabled');
        yield BooleanField::new('isVerified');
        yield TextField::new('confirmation_code');
        yield TextField::new('first_name');
        yield TextField::new('last_name');
        yield IntegerField::new('customer_id')->setDisabled();
        yield CollectionField::new('orders')->useEntryCrudForm(OrderCrudController::class)
            ->setEntryIsComplex();
        $password = TextField::new('password')->setFormType(PasswordType::class);

        if (Crud::PAGE_NEW === $pageName) {
            yield $password;
        }


        //];

        //yield TextField::new('password');
    }

    public function configureActions(Actions $actions): Actions
    {
        $impersonate = Action::new('impersonate', 'Impersonate')
//            ->linkToRoute('admin', function (User $entity) {
//                return [
//                    'id' => $entity->getId(),
//                    '_switch_user' => $entity->getEmail()
//                    // removed ? before _switch_user
//                ];
//            })
            ->linkToUrl(function (User $entity) {
                return 'admin/?_switch_user='.$entity->getEmail();
            });

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $impersonate);
    }

}
