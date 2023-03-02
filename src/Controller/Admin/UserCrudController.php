<?php

namespace App\Controller\Admin;

use PDO;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use function Symfony\Component\Translation\t;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('edit', 'Mon profil');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $user = $this->getUser()->getStatut();
            if ($user == "freelance" || $user == "educatheure") {
                $email = $_SESSION['_sf2_attributes']["_security.last_username"];
                // dump($email);
                $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
                $response->andWhere('entity.email = :email')->setParameter('email', $email);
                return $response;
            } else {
                $response = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
                return $response;
            }
               
    }


    public function configureActions(Actions $actions): Actions
    {
        // $dsn = 'mysql:host=localhost;dbname=projet_adepa;port=3306;charset=UTF8';
        // $pdo = new PDO($dsn, 'root', '');
        $dsn = 'mysql:host=oliadkuxrl9xdugh.chr7pe7iynqr.eu-west-1.rds.amazonaws.com;dbname=jwanvz1y992vdg0l;port=3306;charset=UTF8';
        $pdo = new PDO($dsn, 'uc2ydizx1qegaq18', 'e3g8cfvp8ctyiykz');

        $email = $_SESSION['_sf2_attributes']["_security.last_username"];

        $query = $pdo->query("SELECT * FROM user WHERE email='".$email."'");
        $user = $query->fetch();
        

        if (true) {
            dump($user);
            return $actions
                ->remove(Crud::PAGE_INDEX, Action::NEW);
        }else {
            return $actions;
        }
    }

    public function configureFields(string $pageName): iterable
    {

        $email = $_SESSION['_sf2_attributes']["_security.last_username"];

        // $dsn = 'mysql:host=oliadkuxrl9xdugh.chr7pe7iynqr.eu-west-1.rds.amazonaws.com;dbname=jwanvz1y992vdg0l;port=3306;charset=UTF8';
        // $pdo = new PDO($dsn, 'uc2ydizx1qegaq18', 'e3g8cfvp8ctyiykz');

        // $dsn = 'mysql:host=localhost;dbname=projet_adepa;port=3306;charset=UTF8';
        // $pdo = new PDO($dsn, 'root', '');
        $dsn = 'mysql:host=oliadkuxrl9xdugh.chr7pe7iynqr.eu-west-1.rds.amazonaws.com;dbname=jwanvz1y992vdg0l;port=3306;charset=UTF8';
        $pdo = new PDO($dsn, 'uc2ydizx1qegaq18', 'e3g8cfvp8ctyiykz');

        $query = $pdo->query("SELECT * FROM user WHERE email='".$email."'");
        $user = $query->fetch();
        
        $test = 'test';
        // dump($user['roles']);

        yield IntegerField::new('id')
            ->hideOnForm();

        yield FormField::addPanel('Obligatoire : choix de votre profil');
        yield ChoiceField::new('profil')
            ->setLabel("Je souhaite :")
            ->setHelp('Choix unique')
            ->setChoices([
                "Proposer des ateliers" => "atelier",
                "Proposer ses services comme Educat'heure" => "educ",
                "Les deux" => "all",
            ])
            ->renderExpanded();
  
        yield FormField::addPanel('Civilité');
        yield TextField::new('lastname')
            ->setLabel('Nom');
        yield TextField::new('firstname')
            ->setLabel('Prénom');

        yield FormField::addPanel('Contact');
        yield TextField::new('email');
        yield TextField::new('telephone')
            ->hideOnIndex()
            ->setLabel('Téléphone');


        
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
    
        // if ($user['roles'] == '["ROLE_ADMIN"]' ) {
        if (true) {
            yield FormField::addPanel('Gestion des privilèges');
            yield ChoiceField::new('roles')
            ->setLabel('Rôle')
            ->setChoices(array_combine($roles, $roles))
            ->allowMultipleChoices()
            ->renderExpanded()
            ->hideOnForm()
            ->hideOnDetail()
            ->hideOnIndex()
            ->renderAsBadges();
        } else {
            yield ChoiceField::new('roles')
            ->setLabel('Rôle')
            ->setChoices(array_combine($roles, $roles))
            ->allowMultipleChoices()
            ->renderExpanded()
            ->hideOnForm()
            ->hideOnDetail()
            ->hideOnIndex()
            ->renderAsBadges();
        }
       
        if (true ) {
        yield TextField::new('password')
            ->hideOnIndex()
            ->hideOnDetail()
            ->setLabel('Mot de passe');
        } else {
        yield TextField::new('password')
            ->hideOnIndex()
            ->hideOnDetail()
            ->hideOnForm()
            ->setLabel('Mot de passe');
        }


        $statut = ['admin', 'freelance', 'educat\'heure'];
        if ($user['roles'] == '["ROLE_ADMIN"]' ) {
            yield ChoiceField::new('statut')
                ->setLabel('Statut')
                ->setTranslatableChoices([
                    'admin' => t('Admin'),
                    'freelance' => t('Freelance'),
                    'educatheure' => 'Educat\'Heure',
                ]);
        }else {
            yield ChoiceField::new('statut')
                ->setLabel('Statut')
                ->setTranslatableChoices([
                    'admin' => t('Admin'),
                    'freelance' => t('Freelance'),
                    'educatheure' => 'Educat\'Heure',
                ])
                ->hideOnForm();
        }
         
        yield FormField::addPanel('Adresse');
        yield TextField::new('adress')
        ->hideOnIndex()
        ->setLabel('Adresse');
        yield IntegerField::new('zip_code')
        ->hideOnIndex()
        ->setLabel('Code Postal');
        yield TextField::new('city')
        ->hideOnIndex()
        ->setLabel('Ville');

        yield FormField::addPanel('Informations bancaires et juridiques');
        yield TextField::new('iban')
            ->hideOnIndex()
            ->setLabel('IBAN');
        yield TextField::new('siret')
            ->hideOnIndex()
            ->setLabel('SIRET');
        yield TextField::new('tva')
            ->hideOnIndex()
            ->setLabel('TVA');

        yield FormField::addPanel('Documents divers');

        yield ImageField::new('cni')
            ->hideOnIndex()
            ->hideOnDetail()
            ->setFormType(FileUploadType::class)
            ->setLabel('Carte d\'identité')
            ->setBasePath('uploads/pdf/')
            ->setUploadDir('public/uploads/pdf')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setFormTypeOptions(['attr' => [
                'accept' => 'application/pdf'
                ]   
            ]);
        yield TextField::new('cni')->setTemplatePath('admin/fields/document_link.html.twig')
            ->setLabel("Carte identité")
            ->hideOnForm();

        yield ImageField::new('criminal_record')
            ->setFormType(FileUploadType::class)
            ->setLabel('Casier judiciaire')
            ->hideOnIndex()
            ->hideOnDetail()
            ->setBasePath('uploads/pdf/')
            ->setUploadDir('public/uploads/pdf')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setFormTypeOptions(['attr' => [
                'accept' => 'application/pdf'
                ]   
            ])
            ->setLabel("Casier judiciaire");
        yield TextField::new('criminal_record')->setTemplatePath('admin/fields/document_link.html.twig')
            ->hideOnForm()
            ->setLabel("Casier judiciaire");

        yield ImageField::new('driver_license')
            ->hideOnIndex()
            ->hideOnDetail()
            ->setFormType(FileUploadType::class)
            ->setLabel('Permis de conduire')
            ->setBasePath('uploads/pdf/')
            ->setUploadDir('public/uploads/pdf')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setFormTypeOptions(['attr' => [
                'accept' => 'application/pdf'
                ]   
            ]);
        yield TextField::new('driver_license')->setTemplatePath('admin/fields/document_link.html.twig')
            ->hideOnForm()
            ->setLabel("Permis de conduire");

        yield ImageField::new('autoentreprise_certificate')
            ->hideOnIndex()
            ->setFormType(FileUploadType::class)
            ->setLabel('Attestation autoentrepreneur')
            ->hideOnDetail()
            ->setBasePath('uploads/pdf/')
            ->setUploadDir('public/uploads/pdf')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setFormTypeOptions(['attr' => [
                'accept' => 'application/pdf'
                ]   
            ])
            ->setTemplatePath('admin/fields/document_link.html.twig');

        yield TextField::new('autoentreprise_certificate')
            ->hideOnForm()
            ->setLabel('Attestation autoentrepreneur');          
            


        yield FormField::addPanel();

        yield DateTimeField::new('createdAt')
            ->setLabel('Crée le')
            ->hideOnForm();

        yield DateTimeField::new('updatedAt')
            ->setLabel('Modifié le')
            ->hideOnForm();

        yield DateTimeField::new('deletedAt')
            ->setLabel('Supprimé le')
            ->hideOnForm();
    }

    
}
