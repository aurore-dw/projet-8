# Projet 8 Formation Développeur d'application - PHP/Symfony Openclassrooms

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/1ac5421b3a2249759903e4a53298df3f)](https://app.codacy.com/gh/aurore-dw/projet-8-new/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/1ac5421b3a2249759903e4a53298df3f)](https://app.codacy.com/gh/aurore-dw/projet-8-new/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_coverage)

## Contexte :

Vous venez d’intégrer une startup dont le cœur de métier est une application permettant de gérer ses tâches quotidiennes. L’entreprise vient tout juste d’être montée, et l’application a dû être développée à toute vitesse pour permettre de montrer à de potentiels investisseurs que le concept est viable (on parle de Minimum Viable Product ou MVP).

Le choix du développeur précédent a été d’utiliser le framework PHP Symfony, un framework que vous commencez à bien connaître ! 

Bonne nouvelle ! ToDo & Co a enfin réussi à lever des fonds pour permettre le développement de l’entreprise et surtout de l’application.

Votre rôle ici est donc d’améliorer la qualité de l’application. La qualité est un concept qui englobe bon nombre de sujets : on parle souvent de qualité de code, mais il y a également la qualité perçue par l’utilisateur de l’application ou encore la qualité perçue par les collaborateurs de l’entreprise, et enfin la qualité que vous percevez lorsqu’il vous faut travailler sur le projet.

Ainsi, pour ce dernier projet de spécialisation, vous êtes dans la peau d’un développeur expérimenté en charge des tâches suivantes :

- L’implémentation de nouvelles fonctionnalités ;
- La correction de quelques anomalies ;
- L’implémentation de tests automatisés.
Il vous est également demandé d’analyser le projet grâce à des outils vous permettant d’avoir une vision d’ensemble de la qualité du code et des différents axes de performance de l’application.

Il ne vous est pas demandé de corriger les points remontés par l’audit de qualité de code et de performance. Cela dit, si le temps vous le permet, ToDo & Co sera ravi que vous réduisiez la dette technique de cette application.

## Description du besoin :

### Corrections d'anomalies

#### Une tâche doit être attachée à un utilisateur

Actuellement, lorsqu’une tâche est créée, elle n’est pas rattachée à un utilisateur. Il vous est demandé d’apporter les corrections nécessaires afin qu’automatiquement, à la sauvegarde de la tâche, l’utilisateur authentifié soit rattaché à la tâche nouvellement créée.

Lors de la modification de la tâche, l’auteur ne peut pas être modifié.

Pour les tâches déjà créées, il faut qu’elles soient rattachées à un utilisateur “anonyme”.

#### Choisir un rôle pour un utilisateur

Lors de la création d’un utilisateur, il doit être possible de choisir un rôle pour celui-ci. Les rôles listés sont les suivants :

rôle utilisateur (ROLE_USER) ;
rôle administrateur (ROLE_ADMIN).
Lors de la modification d’un utilisateur, il est également possible de changer le rôle d’un utilisateur.

### Implémentation de nouvelles fonctionnalités

#### Autorisation

Seuls les utilisateurs ayant le rôle administrateur (ROLE_ADMIN) doivent pouvoir accéder aux pages de gestion des utilisateurs.

Les tâches ne peuvent être supprimées que par les utilisateurs ayant créé les tâches en question.

Les tâches rattachées à l’utilisateur “anonyme” peuvent être supprimées uniquement par les utilisateurs ayant le rôle administrateur (ROLE_ADMIN).

Implémentation de tests automatisés
Il vous est demandé d’implémenter les tests automatisés (tests unitaires et fonctionnels) nécessaires pour assurer que le fonctionnement de l’application est bien en adéquation avec les demandes.

Ces tests doivent être implémentés avec PHPUnit ; vous pouvez aussi utiliser Behat pour la partie fonctionnelle.

Vous prévoirez des données de tests afin de pouvoir prouver le fonctionnement dans les cas explicités dans ce document.

Il vous est demandé de fournir un rapport de couverture de code au terme du projet. Il faut que le taux de couverture soit supérieur à 70 %.

### Documentation technique

Il vous est demandé de produire une documentation expliquant comment l’implémentation de l'authentification a été faite. Cette documentation se destine aux prochains développeurs juniors qui rejoindront l’équipe dans quelques semaines. Dans cette documentation, il doit être possible pour un débutant avec le framework Symfony de :

- Comprendre quel(s) fichier(s) il faut modifier et pourquoi ;
- Comment s’opère l’authentification ;
- Où sont stockés les utilisateurs.
S’il vous semble important de mentionner d’autres informations , n’hésitez pas à le faire.

Par ailleurs, vous ouvrez la marche en matière de collaboration à plusieurs sur ce projet. Il vous est également demandé de produire un document expliquant comment devront procéder tous les développeurs souhaitant apporter des modifications au projet.

Ce document devra aussi détailler le processus de qualité à utiliser ainsi que les règles à respecter.

### Audit de qualité du code & performance de l'application

Les fondateurs souhaitent pérenniser le développement de l’application. Cela dit, ils souhaitent dans un premier temps faire un état des lieux de la dette technique de l’application.

Au terme de votre travail effectué sur l’application, il vous est demandé de produire un audit de code sur les deux axes suivants : la qualité de code et la performance.

Bien évidemment, il vous est fortement conseillé d’utiliser des outils vous permettant d’avoir des métriques pour appuyer vos propos.

Vous pouvez par exemple utiliser Codacy ou CodeClimate pour auditer la qualité du code. Pour l'audit de performance, utilisez un outil de profiling. Le profiler de Symfony peut suffire, mais vous pouvez également utiliser Blackfire ou New Relic si vous le souhaitez. Pensez à faire un audit avant et après modification.

## Guide d'installation :

1. Clonez ou télécharger le repository GitHub

- `git clone https://github.com/aurore-dw/projet-8.git`

2. Configurez vos variables d'environnement tel que la connexion à la base de données dans le fichier .env

3. Dans le projet, téléchargez et installez composer et yarn
   
- `composer install`
- `yarn install`

4. Créer un build d'assets grâce à Webpack Encore avec yarn

- `yarn run build`

5. Mettre en place la base de donnée
   
- `php bin/console doctrine:database:create`
- `php bin/console doctrine:migrations:migrate`
- `php bin/console doctrine:schema:update --force`

6. Implémenter les fixtures
   
- `php bin/console doctrine:fixtures:load`

7. Démarrer le serveur web local de Symfony
   
- `symfony server:start`

8. Lancer Webpack encore

- `yarn run watch`

9. Accéder à l'application, généralement `http://localhost:8000`.

10. Les utilisateurs par défaut 

| user   | password | role       |
| ------ | -------- | ---------- |
| user0  | 1234     | ROLE_USER  |
| user1  | 1234     | ROLE_USER  |
| admin0 | 1234     | ROLE_ADMIN |
| admin1 | 1234     | ROLE_ADMIN |

## Pour contribuer au projet :

- Accéder au fichier contribute.md, dans le dossier docs

## Rapport de couverture html des tests unitaires :

- Il se trouve dans le dossier public/test-coverage

## Rapport de qualité du code :

- Il est accessible via le badge en haut du README, qui redirigera vers le projet sur Codacy

## Diagrammes UML :

- Ils sont dans le dossier docs/Diagrammes
