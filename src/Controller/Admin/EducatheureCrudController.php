<?php

namespace App\Controller\Admin;

use App\Entity\Educatheure;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class EducatheureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Educatheure::class;
    }

    public function createEntity(string $entityFqcn)
    {

        $booked = new Educatheure();
        $booked->setBooked("no");
        
        return $booked;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->getUser()->getStatut();
        
            if ($user == "freelance") {
                $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
                $response->andWhere('entity.creator = :creator')->setParameter('creator', $this->getUser());
                return $response;
            } else {
                $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
                return $response;
            }
               
    }

    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Mission Educatheur')
            ->setPageTitle('index', 'Liste de vos propositions de mission');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')
            ->hideOnForm();
        yield TextField::new('title')
            ->setLabel('Intitulé');
        yield ChoiceField::new("type")
            ->setLabel("Catégorie")
            ->setChoices([
                'Activité manuelle' => '1',
                'Handi-sport' => '2',
                'Sport' => '3',
                'Educatif' => '4',
                'Socio-esthétique' => '5'
            ]);
        yield TextField::new('decription')
            ->setLabel('Description');
        yield DateTimeField::new('start_date')
            ->setLabel('Date et heure de début');
        yield DateTimeField::new('end_date')
            ->setLabel('Date et heure de fin');
        yield TextField::new('place')
            ->setLabel('Lieu');
        yield ImageField::new('file')
            ->setFormType(FileUploadType::class)
            ->setLabel('Fiche de poste en format PDF')
            ->setBasePath('uploads/pdf/')
            ->setUploadDir('public/uploads/pdf')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setFormTypeOptions(['attr' => [
                'accept' => 'application/pdf'
            ]
            ]);

        yield TextField::new('file')->setTemplatePath('admin/fields/document_link.html.twig')->onlyOnIndex();

        yield BooleanField::new('urgent')
            -> setLabel('Urgent');
        

        yield AssociationField::new('creator')
            ->setLabel('Créateur')
            ->setQueryBuilder(function (QueryBuilder $qb) {
                $qb->andWhere('entity.email = :session')
                    ->setParameter('session', $this->getUser()->getEmail());
            });           
       
    }
}
