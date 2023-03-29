<?php

namespace App\DataFixtures;

use App\Constant\GenderConstant;
use App\Constant\UserConstant;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Mission;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordEncoder;
    private SluggerInterface $slugger;

    public function __construct(
        UserPasswordHasherInterface $passwordEncoder,
        SluggerInterface $slugger
    ){
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $admin = new User();
        $admin->setEmail('admin@demo.fr');
        $admin->setGender(GenderConstant::MONSIEUR);
        $admin->setLastname($faker->lastName);
        $admin->setFirstname($faker->firstName);
        $admin->setAdress($faker->streetAddress);
        $admin->setTelephone('0606060606');
        $admin->setZipcode((int) str_replace(' ', '', $faker->postcode));
        $admin->setCity($faker->city);
        $admin->setIsVerified(true);
        $admin->setEnabled(true);
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'toto')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);


        $subCategorie = new Category();
        $subCategorie->setTitle('Aide personne agée');
        $manager->persist($subCategorie);

        $this->addReference('sub-category', $subCategorie);

        $subC = $this->getReference('sub-category');

        $categorie = new Category();
        $categorie->setTitle('Public');
        $categorie->setParent($subC);

        $manager->persist($categorie);


        for($usr = 1; $usr <= 100; $usr++){

            $genres = GenderConstant::MAP;
            shuffle($genres);

            $user = new User();
            $user->setEmail($faker->email);
            $user->setGender(array_pop($genres));
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $admin->setTelephone('0606060606');
            $user->setAdress($faker->streetAddress);
            $admin->setIsVerified(true);
            $admin->setEnabled(true);
            $user->setZipcode((int) str_replace(' ', '', $faker->postcode));
            $user->setCity($faker->city);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'toto')
            );

            $roles = [
                UserConstant::ROLE_ADMIN,
                UserConstant::ROLE_FREELANCE,
                UserConstant::ROLE_CLIENT
            ];
            array_shift($roles);
            $role = array_pop($roles);
            $user->setRoles([$role]);

            $manager->persist($user);

            $user->addCategory($subC);

            $mission = new Mission();
            $mission->setTitle($faker->words(3, true));
            $mission->setContent($faker->words(200, true));

            $jours = random_int(5,70);

            $mission->setStarted(new \DateTime(sprintf('+%d days', $jours)));
            $mission->setEnded(new \DateTime(sprintf('+%d days', $jours + 15)));
            $mission->setPublished(true);
            $mission->setAddress($faker->streetAddress);
            $mission->setZipcode((int) str_replace(' ', '', $faker->postcode));
            $mission->setCity($faker->city);

            $this->addReference('user-'.$usr, $user);

            $t = $this->getReference('user-'.$usr);

            $mission->setUser($t);

            $mission->addCategory($subC);

            $manager->persist($mission);

            $article = new Article();
            $article->setTitle($faker->words(3, true));
            $article->setContent($faker->words(1000, true));
            $article->setPublished(true);

            $t = $this->getReference('user-'.$usr);

            $article->setUser($t);

            $manager->persist($article);

        }

        $manager->flush();
    }
}
