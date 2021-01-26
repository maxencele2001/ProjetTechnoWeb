# Projet Technologies Web

<a href="https://github.com/maxencele2001/ProjetTechnoWeb">
    <img src="public/img/logo.png" alt="Logo" width="80" height="80">
  </a>

Projet d'étude de deuxième année de Bachelor | Informatique Ingésup du Campus YNOV Lyon.
Ce projet permet l’évaluation des compétences acquises grâce aux modules de l’UF « Technologies Web ».

Projet web Deliveroo : création d’un site de commandes et livraisons de repas

Son aboutissement est un site web :
Dynamique
- Ergonomique
- Responsive
- Accessible pour tous 

Ce dépot concerne l'équipe 12 composé de Maxence CROSSSE, Aurélien ROBIER et Ebbane DIET

## Pour commencer

Vous trouverez dans ce dossier le site web avec la partie programmation réalisée en PHP (symfony), HTML et CSS (+ Bootstrap)

Vous trouverez aussi la base de données en piece jointe.



## Démarrer

Ce qu'il est requis pour commencer c'est d'avoir le fichier .sql. Ce site est relié a une base de données, pour utiliser pleinement le site et ses fonctionnalités, il faut avec le fichier SQL :

- Utiliser Mysql ( utilisable avec WampServer -> phpMyAdmin -> MySQL) : veuillez créer un fichier .env.local pour indiquer vos paramètres de connexion à votre base de données

- télécharger Composer



### Installation


1. Cloner le repo
   ```sh
   git clone https://github.com/maxencele2001/ProjetTechnoWeb
   ```
2. Installer les dépendances
   ```sh
   composer install
   ```
3. Faire les migrations
```sh
   php bin/console doctrine:schema:update --force
   ```


## Fabriqué avec


* [Bootstrap](https://getbootstrap.com/) - Framework CSS (front-end)
* [Visuel Studio Code](https://code.visualstudio.com/) - Editeur de textes
* [PHP - Symfony ](https://www.php.net/) - Langage de programmation qui génère du contenu web dynamique
* [MySQL](https://www.mysql.com/fr/) - système de gestion de bases de données

[![forthebadge](https://forthebadge.com/images/badges/made-with-javascript.svg)](http://forthebadge.com)  [![forthebadge](https://forthebadge.com/images/badges/uses-html.svg)](http://forthebadge.com)  [![forthebadge](https://forthebadge.com/images/badges/uses-css.svg)](http://forthebadge.com)


## Auteurs

* **CROSSE Maxence** _alias_ [@Maxencele2001](https://github.com/maxencele2001)
* **ROBIER Aurélien** _alias_ [@redwingss](https://github.com/redwingss)
* **DIET Ebbane** _alias_ [@ebbane](https://github.com/ebbane)

## GitHub

Ce projet est disponible sur GitHub

* [Dépot](https://github.com/maxencele2001/ProjetTechnoWeb.git)
