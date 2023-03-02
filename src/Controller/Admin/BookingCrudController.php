<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Booking;
use App\Entity\Product;
use App\Entity\Educatheure;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->getUser()->getStatut();
        
            if ($user == "freelance") {
                $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
                $response->andWhere('entity.user = :user')->setParameter('user', $this->getUser());
                return $response;
            } else {
                $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
                return $response;
            }
               
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Liste de vos réservations')
            ->setPageTitle('index', 'Liste de vos réservations');
    }

    
    public function configureFields(string $pageName): iterable
    {

        yield IdField::new('id')
            ->setLabel('Références');


        yield AssociationField::new('educ_id')
            ->setLabel('Mission');
        
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $booking): void
    {

        // Récupérer l'id de la mission educatheure dont la réservation va être supprimé
        $missionId = $booking->getEducId();

        // Récupérer la mission concernée par la suppression
        $repository = $entityManager->getRepository(Educatheure::class);
        $mission = $repository->find($missionId);


        // A la suppression dire que la mission est à nouveau non réservée. 

        // Supprimer la réservation
        $entityManager->remove($booking);
        $mission->setBooked("no");
        $entityManager->flush();
        
    }

}