<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégorie')
            ->setPageTitle('index', 'Liste des catégories');
    }

    
    public function configureFields(string $pageName): iterable
    {
       yield TextField::new('name')
        ->setLabel("Catégorie ");

       yield ChoiceField::new("type")
       ->setLabel('Type')
        -> setChoices([
            "Tous" => "all",
            "Ateliers" => "event",
            "Educatheure" => "mission"
        ]);
    }
    
}
