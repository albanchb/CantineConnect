## **CantineConnect**

**CantineConnect** est une plateforme web conçue pour faciliter la gestion des réservations dans les cantines scolaires, entreprises ou tout autre établissement nécessitant un système de gestion de repas. L'application permet aux utilisateurs de réserver leur repas, de gérer leurs commandes, et aux administrateurs de suivre les réservations via un panneau d'administration.

Ce projet a été réalisé dans un cadre personnel, dans le but de mieux comprendre la gestion de bases de données et l'intégration de fonctionnalités de réservation en ligne.

---

## **Fonctionnalités principales**

### **Pour les utilisateurs :**
- **Réservation de repas :** Les utilisateurs peuvent choisir la date de leur réservation et le nombre de repas.
- **Connexion/inscription :** Système d'inscription et de connexion pour gérer les réservations personnelles.
- **Gestion de compte :** L'utilisateur peut modifier ses informations personnelles.

### **Pour les administrateurs :**
- **Panneau d'administration :** Les administrateurs peuvent gérer les utilisateurs, voir les réservations, et réinitialiser les mots de passe des utilisateurs.
- **Gestion des réservations :** L'administrateur peut consulter toutes les réservations effectuées et gérer les demandes.
- **Page de statistiques (`stats.php`)** : Cette page permet aux administrateurs de voir toutes les réservations effectuées sur la plateforme et d'analyser les tendances de réservation.

---

## **Pages principales du projet**

### **1. reservation.php**
La page `reservation.php` permet aux utilisateurs de réserver un repas. Un formulaire permet de sélectionner la date et le nombre de repas. Seuls les utilisateurs connectés peuvent effectuer une réservation.

### **2. dashboard.php**
Le panneau d'administration permet aux administrateurs de gérer les utilisateurs et les réservations. Les administrateurs peuvent voir la liste des utilisateurs, réinitialiser les mots de passe et gérer les réservations.

### **3. login.php**
La page de connexion permet aux utilisateurs et administrateurs de se connecter à leur compte. Elle vérifie les identifiants et crée une session pour l'utilisateur.

### **4. register.php**
La page d'inscription permet à un utilisateur de créer un compte. Lors de l'inscription, un email est envoyé pour confirmer l'adresse de l'utilisateur.

### **5. stats.php**
La page `stats.php` permet aux administrateurs de voir les réservations effectuées par les utilisateurs. Elle offre des statistiques pour faciliter la gestion des repas.

---

## **Technologies utilisées**

- **PHP** : Pour le back-end, la gestion des connexions et des interactions avec la base de données.
- **MySQL** : Pour le stockage des utilisateurs, des réservations et des paramètres de l'application.
- **HTML/CSS** : Pour le front-end et la mise en page des différentes pages.
- **JavaScript** : Pour la gestion dynamique de certaines fonctionnalités.

---

## **Installation**

### Prérequis :
- Un serveur web avec PHP et MySQL (ex. : XAMPP, WAMP, LAMP).
- Un éditeur de texte (ex. : VSCode, Sublime Text).
  
### Étapes d'installation :

1. **Clonez le repository :**
   ```bash
   git clone https://github.com/albanchb/CantineConnect.git
