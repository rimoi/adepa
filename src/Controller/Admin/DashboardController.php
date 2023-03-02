<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\Category;
use App\Entity\Educatheure;
use PDO;
use App\Entity\News;
use App\Entity\User;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Les extras ADEPA');
    }



    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->addMenuItems([
                MenuItem::linkToUrl('Retour à l\'accueil', 'fas fa-user', $this->generateUrl('home'))
            ]);
    }

    public function configureMenuItems(): iterable
    {        
//        $email = $_SESSION['_sf2_attributes']["_security.last_username"];

//        $pdo = new PDO('mysql:host=sql849.main-hosting.eu;dbname=u266887992_adepa;charset=utf8', 'u266887992_admin', 'Test12345678');

//        $query = $pdo->query("SELECT id FROM user WHERE email='".$email."'");
//        $user = $query->fetch();

//        $statut = $pdo->query("SELECT statut FROM user WHERE email='".$email."'")->fetch();

        yield MenuItem::linkToUrl('Accueil', 'fa-solid fa-house', $this->generateUrl('home'));
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::linkToCrud('Gestion des utilisateurs', 'fas fa-list', User::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Gestion des educatheures', 'fas fa-list', Product::class)
            ->setPermission("ROLE_ADMIN");

//        if ($statut['statut'] == "admin" || $statut['statut'] == "client") {
//            yield MenuItem::linkToCrud('Gestion des missions Educatheure', 'fas fa-list', Educatheure::class);
//        }

        yield MenuItem::linkToCrud("Gérer les catégories", "fa-regular fa-box", Category::class)
            ->setPermission('ROLE_ADMIN');
        
        yield MenuItem::linkToCrud('Créer une actualité', 'fas fa-list', News::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Mes extras', 'fas fa-list', Booking::class);
        yield MenuItem::linkToUrl('Calendrier', 'fa-regular fa-calendar', $this->generateUrl('calendar'));
        yield MenuItem::linkToCrud('Mon profil', 'fa-solid fa-user', User::class)
            ->setAction(Action::EDIT)
            ->setEntityId(1)
            ->setPermission('ROLE_USER');
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    // public function configureAssets(): Assets
    // {
    //     return parent::configureAssets()
    //         // ->addWebpackEncoreEntry('admin')
    //         ->addCssFile('https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css')
    //         ->addJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js');

    // }
    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        //Connexion à la base de donnée du Wordpress afin de récupérer les articles pour le widget actualité 

//        $dsn = 'mysql:host=oliadkuxrl9xdugh.chr7pe7iynqr.eu-west-1.rds.amazonaws.com;dbname=jwanvz1y992vdg0l;port=3306;charset=UTF8';
//        $pdo = new PDO($dsn, 'uc2ydizx1qegaq18', 'e3g8cfvp8ctyiykz');

//        $query = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
//        $news = $query->fetchAll();

        // Récupération statistique nombres utilisateurs
//        $nbrUserQuery = $pdo->query("SELECT COUNT(*) AS total FROM user");
//        $nbrUser = $nbrUserQuery->fetch();

        // Récupération statistique nombres évènement
//        $nbrEventQuery = $pdo->query("SELECT COUNT(*) AS total FROM product");
//        $nbrEvent = $nbrEventQuery->fetch();
        // dump($nbrUser['total']);

        

        return $this->render('admin/index.html.twig', [
            "news" => ['id' =>1, 'title' => 'gdjd'],
            "nbrUser" => ['total' =>14],
            "nbrEvent" => ['total' =>10]
        ]);
    }
}
