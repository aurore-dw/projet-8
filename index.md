# Guide de Collaboration sur un Projet Symfony avec GitHub

## 1. Utilisation des Issues :

### a. Principe :
Les issues sont des points spécifiques à résoudre ou des fonctionnalités à implémenter dans le projet. Elles servent de base pour suivre le progrès et la répartition des tâches.

### b. Création d'une Issue :
- Accédez à l'onglet "Issues" sur GitHub.
- Cliquez sur "New issue" pour créer une nouvelle issue.
- Décrivez clairement le problème ou la fonctionnalité souhaitée.

### c. Attribution de l'Issue :
- Utilisez l'option "Assignees" pour attribuer l'issue à un collaborateur.
- Indiquez clairement qui est responsable de résoudre cette issue.

## 2. Création d'une Branche en Fonction de l'Issue :

### a. Principe :
Chaque issue ou fonctionnalité à implémenter devrait avoir sa propre branche pour isoler les changements et faciliter la gestion du code.

### b. Création de la Branche :
- Utilisez la commande `git checkout -b nom_de_la_branche` en remplaçant `nom_de_la_branche` par un nom significatif, idéalement lié à l'issue.
- Exemple : `git checkout -b issue-1-ajout-connexion`.

## 3. Commits :

### a. Messages de Commit Explicites :
- Chaque commit devrait avoir un message clair et explicite décrivant les changements apportés.
- Vous pouvez inclure des références aux issues GitHub si nécessaire.

### b. Commit Régulièrement :
1. Ajoutez les fichiers modifiés ou ajoutés à l'index avec la commande `git add nom_du_fichier` ou `git add .` pour ajouter tous les fichiers modifiés.
2. Une fois les fichiers ajoutés à l'index, créez un commit en utilisant la commande `git commit -m "Message de commit ici"`.
3. Remplacez "Message de commit ici" par un message clair et concis décrivant les modifications apportées dans ce commit.

## 4. Validation d'un Pull Request :

### a. Principe :
Une fois que vous avez terminé de travailler sur une fonctionnalité ou de résoudre un problème, vous pouvez créer un pull request pour fusionner votre branche avec la branche principale du projet.

### b. Création du Pull Request :
- Accédez à l'onglet "Pull requests" sur GitHub.
- Cliquez sur "New pull request" pour créer un nouveau pull request.
- Sélectionnez les branches à fusionner.

### c. Validation par un Autre Collaborateur :
- Un autre collaborateur passe en revue votre code et donne son approbation, pose des questions ou demande des modifications si nécessaire.
- La personne qui vérifie le pull request peut ne pas être celle qui a créé l'issue.

### d. Fusion du Pull Request :
- Une fois que le pull request a été approuvé, vous pouvez le fusionner en cliquant sur le bouton "Merge pull request". La branche principale du projet sera alors mise à jour. Si le pull request contient tout le code demandé dans l’issue, cette dernière peut être clôturée.
- On peut suivre les pull request directement dans les issues.
