<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{

    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }


    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function createEntity(string $entityFqcn)
    {

        $booked = new Product();
        $booked->setBooked("no");

        $url = $this->adminUrlGenerator
            ->setController(ProductCrudController::class)
            ->setAction('new')
            ->generateUrl();
        
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
            ->setEntityLabelInSingular('Prestation')
            ->setPageTitle('index', 'Liste de vos prestations');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')
            ->hideOnForm();
        yield TextField::new('title')
            ->setLabel('Intitulé');

        yield ChoiceField::new("categoryTest")
            ->setChoices([
                'Activité manuelle' => '1',
                'Handi-sport' => '2',
                'Sport' => '3',
                'Educatif' => '4',
                'Socio-esthétique' => '5'
            ])
            ->renderExpanded()
            ->setLabel("Choisir UNE catégorie pour l'activité");
           

        yield ImageField::new('photo')
            ->setBasePath('uploads/images/')
            ->setUploadDir('public/uploads/images')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setLabel('Photo');

        yield TextField::new('description')
            ->setLabel('Description');

        yield IntegerField::new("participantMax")
            ->setLabel("Indiquer le nombre maximum de participant");


        yield DateTimeField::new('start_date')
            ->setLabel('Date et heure de début');
        yield DateTimeField::new('end_date')
            ->setLabel('Date et heure de fin');
        yield TextField::new('place')
            ->setLabel('Lieu');
        yield MoneyField::new('price')
            ->setCurrency('EUR')
            ->setLabel('Prix');

        yield ImageField::new('file')
            ->setFormType(FileUploadType::class)
            ->setLabel('Fichier PDF')
            ->setBasePath('uploads/pdf/')
            ->setUploadDir('public/uploads/pdf')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setFormTypeOptions(['attr' => [
                'accept' => 'application/pdf'
            ]
            ]);

        yield TextField::new('file')->setTemplatePath('admin/fields/document_link.html.twig')->onlyOnIndex();
        

        yield AssociationField::new('creator')
            ->setLabel('Créateur')
            ->setQueryBuilder(function (QueryBuilder $qb) {
                $qb->andWhere('entity.email = :session')
                    ->setParameter('session', $this->getUser()->getEmail());
            });           
       
    }
}
