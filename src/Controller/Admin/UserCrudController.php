<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Component\Form\FormEvents;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\FormBuilderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType};

class UserCrudController extends AbstractCrudController
{
    public function __construct(public UserPasswordHasherInterface $userPasswordHasher){}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        $fields = [            
            EmailField::new('email'),
            ArrayField::new('roles'),
            BooleanField::new('is_verified'),
        ];

        $password = TextField::new('password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => '(Repeat)'],
                'mapped' => false,
            ])
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms()
            ;
        $fields[] = $password;

        return $fields;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
        return $this->addPasswordEventListener($formBuilder);
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
    }

    private function hashPassword() {
        return function($event) {
    $form = $event->getForm();
    if (!$form->isValid()) {
        return;
    }
    $password = $form->get('password')->getData();
    if ($password === null) {
        return;
    }
    $hash = $this->userPasswordHasher->hashPassword($this->getUser(), $password);
    $form->getData()->setPassword($hash);
};
    
}

public function configureCrud(Crud $crud): Crud
{
    return $crud
        // the names of the Doctrine entity properties where the search is made on
        // (by default it looks for in all properties)
        ->setSearchFields(['name', 'description'])
        // use dots (e.g. 'seller.email') to search in Doctrine associations
        ->setSearchFields(['name', 'description', 'seller.email', 'seller.address.zipCode'])
        // set it to null to disable and hide the search box
        ->setSearchFields(null)
        // call this method to focus the search input automatically when loading the 'index' page
        ->setAutofocusSearch()

        // defines the initial sorting applied to the list of entities
        // (user can later change this sorting by clicking on the table columns)
        ->setDefaultSort(['id' => 'DESC'])
        ->setDefaultSort(['id' => 'DESC', 'title' => 'ASC', 'startsAt' => 'DESC'])
        // you can sort by Doctrine associations up to two levels
        ->setDefaultSort(['seller.name' => 'ASC'])

        // the max number of entities to display per page
        ->setPaginatorPageSize(30)
        // the number of pages to display on each side of the current page
        // e.g. if num pages = 35, current page = 7 and you set ->setPaginatorRangeSize(4)
        // the paginator displays: [Previous]  1 ... 3  4  5  6  [7]  8  9  10  11 ... 35  [Next]
        // set this number to 0 to display a simple "< Previous | Next >" pager
        ->setPaginatorRangeSize(4)

        // these are advanced options related to Doctrine Pagination
        // (see https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/pagination.html)
        ->setPaginatorUseOutputWalkers(true)
        ->setPaginatorFetchJoinCollection(true)
    ;
}

}
