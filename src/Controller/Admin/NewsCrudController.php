<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\News;
use DateTimeImmutable;
use Symfony\Component\Routing\Route;
use App\Repository\ProductRepository;
use App\Repository\EducatheureRepository;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class NewsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return News::class;
    }

    public function createEntity(string $entityFqcn)
    {

        $news = new News();
        $news->setCreatedAt(new DateTime('now'));
        $news->setUpdatedAt(new DateTime('now'));
        $news->setAuthor($this->getUser());

        return $news;
    }

    
    public function configureFields(string $pageName): iterable
    {

        $title = TextField::new('title')
        ->setLabel('Titre');

        $text = TextEditorField::new('text')
        ->setLabel("Texte");

        $created_at = DateTimeField::new('createdAt')
        ->setLabel('Crée le')
        // ->setEmptyData($date)
        ->hideOnForm();

        $updated_at = DateTimeField::new('updatedAt')
        ->setLabel('Modifié le')
        ->hideOnForm();

        $deleted_at = DateTimeField::new('deletedAt')
        ->setLabel('Supprimé le')
        ->hideOnForm();

        $author = TextField::new('author')
        ->setLabel("Auteur");

        if (Crud::PAGE_NEW === $pageName) {
            return [$title, $text, $created_at, $updated_at];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$title, $text, $created_at, $updated_at];
        }
        else  {
            return [$title, $text, $created_at, $updated_at, $deleted_at, $author];
        }
    }

    #[Route('/information', name: 'detail_news')]
    public function newsDetail(): Response
    {
        return $this->render('/news/news-detail.html.twig');
    }
}
