<?php
require 'config.php';
session_start(); 

$message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = strtolower(trim($_POST['nom'])); 
    $prenom = strtolower(trim($_POST['prenom'])); 
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $internat = isset($_POST['internat']) ? 1 : 0;
    $classe = $_POST['classe'];

    $username = $prenom . '.' . $nom;

    $checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkUsername->bind_param("s", $username);
    $checkUsername->execute();
    $result = $checkUsername->get_result();

    if ($result->num_rows > 0) {
        $message = "Le nom d'utilisateur '$username' est déjà utilisé. Veuillez essayer un autre prénom ou nom.";
    } else {
        $sql = "INSERT INTO users (nom, prenom, username, mot_de_passe, internat, classe) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $nom, $prenom, $username, $mot_de_passe, $internat, $classe);

        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['pseudo'] = $prenom; 

            header("Location: identifiant.php");
            exit();
        } else {
            $message = "Erreur lors de l'inscription. Veuillez réessayer plus tard.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="manifest" href="_manifest.json" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Inscription - CantineConnect</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #FF914D;
            padding: 15px 20px;
            text-align: center;
            color: white;
            font-size: 25px;
        }

        .container {
            flex: 1;
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #FF914D;
            margin-bottom: 20px;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input, select {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: #FF914D;
        }

        button {
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: linear-gradient(90deg, #FF914D 0%, #FF5722 100%);
            color: white;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background: linear-gradient(90deg, #FF5722 0%, #FF914D 100%);
        }

        .login-link {
            text-align: center;
            margin-top: 20px; /* Augmenter la marge pour le séparer du formulaire */
            font-size: 16px;
        }

        .login-link a {
            color: #FF5722; /* Couleur distinctive */
            font-weight: bold; /* Mettre le texte en gras */
            text-decoration: none; /* Supprimer le soulignement */
            border-bottom: 2px solid #FF5722; /* Soulignement par une bordure */
            padding-bottom: 2px; /* Espacement pour la bordure */
            transition: color 0.3s, border-color 0.3s; /* Transition pour un effet doux */
        }

        .login-link a:hover {
            color: #FF914D; /* Couleur au survol */
            border-color: #FF914D; /* Changer la couleur de la bordure au survol */
        }

        footer {
            background: linear-gradient(90deg, #FF914D 0%, #FF5722 100%);
            padding: 20px;
            color: white;
            text-align: center;
            font-size: 14px;
            margin-top: auto;
        }

        .footer-info {
            font-size: 14px;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            header {
                font-size: 20px;
            }

            .container {
                width: 95%;
            }

            input, select {
                padding: 10px;
            }
        }

        @media (max-width: 414px) {
            header {
                font-size: 18px;
            }

            .container {
                padding: 15px;
            }

            button {
                font-size: 0.9em;
            }

            .login-link {
                font-size: 14px;
            }
        }
    </style>
</head>
<header>CantineConnect
    </header>

    <div class="container">
        <h2>Inscription</h2>
        <?php if ($message): ?>
            <p style="color: red;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="prenom" placeholder="Prénom" required>
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>

            <label for="classe">Classe :</label>
            <select name="classe" required>
                <option value="3PM">3PM</option>
                <option value="Seconde Bac Pro">Seconde Bac Pro</option>
                <option value="Premiere Bac PRO">Première Bac PRO</option>
                <option value="Terminal BAC PRO">Terminal BAC PRO</option>
                <option value="Premiere CAP">Première CAP</option>
                <option value="Terminal CAP">Terminal CAP</option>
                <option value="BTS">BTS</option>
            </select>

            <button type="submit">S'inscrire</button>
        </form>
        <div class="login-link">Déjà inscrit ? <a href="login.php">Connectez-vous ici</a></div>
    </div>

    <footer>
        <p>Contacts du lycée : 04 75 07 86 53 - vie-scolaire1.0070031w@ac-grenoble.fr</p>
        <p>ByWeb - Tous droits réservés © 2024</p>
    </footer>
</body>
</html>