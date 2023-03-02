# projet_adepa

--- TO DO --- 
> Faire en sorte que les réservations de mission soient visible par le freelance dans son dashboard sections MES EXTRAS. 
> Si le freelance annule sa mission, il faut que la mission en BDD repasse en booked ? Non. 
> ATTENTION SI TEMPS : mettre en place une image en page accueil par type de poste. 

    --------- QUAND MAILING SERA OPE ------------------
    > Lors de la suppression du booking de la mission : 
        - envoi mail à l'entreprise pour lui prevenir que le prestaire se retire
        - envoi de mail au prestataire/freelance pour lui confirmer la suppression
        - Nouvel envoi de mail à l'ensemble des freelance pour notifier que la mission est à nouveau disponible. 

Pour installer un projet symfony cloner depuis un dépôt distant : 

Intaller le gestionnaire de dépendance afin de récupérer l'ensemble des dépendances du projet (il va utiliser pour cela le fichier package.json)
$ composer install 

Afin de lancer le serveur local :
$ symfony server:start

Installer node  modules :
$ npm install

Mise en place la BDD (https://symfony.com/doc/current/doctrine.html) : 
    Actuellement la BDD est directement relié à la BDD HOSTINGER (absence d'utilisateur )
    Hypothèse : je souhaite travailler en local uniquement 
        => utiliser un serveur local type WAMP
        => créer un dossier .env.local à la racine du projet. Ce fichier prendra automatiquement la main sur .env SI je suis en local. Ce fichier doit être dans le git ignore. 
        => Création de la base de donnée 
            $ php bin/console doctrine:database:create
        => Effectuer la migration
            => $ php bin/console make:migration
            => $ php bin/console doctrine:migrations:migrate


Modification de BDD. Par exemple rajouter une entrée dans la table Booking.
Il faut savoir que doctrine va utiliser les entités afin de créer la BDD. Cette ligne de commande permet la CREATION et l'UPDATE des entités. 

    $ php bin/console make:entity 
    A LA CREATION DES ENTITY : AUTOMATIQUEMENT CREATION DU REPOSITORY QUI VA AVEC


Création des controlleur.
    $ php bin/console make:controller ProductController

PARTIE builder

    Par exemple, j'ai modifié mes assets (ajout d'une nouvelle image, modification de feuille style CSS, ... ) => UTILISATION DE WEBPACK ENCORE. 
    https://symfony.com/doc/current/frontend/encore/simple-example.html

    Plusieurs solutions (allant de la plus utilisée à la moins utilisée) pour compiler les assets

        re-compile à chaque modification du fichier 
        $ npm run watch 

        $ npm run dev 

        au déploiement pour créer le build de prod : 
        $ npm run build

Everything in Encore is configured via a webpack.config.js file at the root of your project. It already holds the basic config you need:

Système email : https://symfony.com/doc/current/mailer.html
    - mise en ligne de la mission par l'entreprise -> affichage accueil -> NOTIFICATION MAIL "nouvelle mission". 
    - 

Pour builder : 



Ré écriture d'URL afin d'avoir des routes fonctionnelles en prod :
/public/htaccess (attention ce dossier ne doit pas modifié en aucune manière).
