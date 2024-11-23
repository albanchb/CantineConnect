<?php
session_start();
require 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT id, mot_de_passe FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && password_verify($mot_de_passe, $result['mot_de_passe'])) {
        $_SESSION['user_id'] = $result['id'];
        header("Location: reservation.php");
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="manifest" href="_manifest.json" />
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - CantineConnect</title>
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
            max-width: 400px;
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

        input {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus {
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

        .forgot-password {
            text-align: center;
            margin-top: 10px;
            font-size: 16px;
        }

        .forgot-password a {
            color: #FF5722;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: #FF914D;
        }

        p {
            text-align: center;
            font-size: 16px;
        }

        p a {
            color: #FF5722;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s;
        }

        p a:hover {
            color: #FF914D;
        }

        .error {
            color: #d32f2f;
            background-color: #ffdddd;
            padding: 10px;
            border-left: 4px solid #f44336;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        footer {
            background-color: #FF914D;
            padding: 20px;
            color: white;
            text-align: center;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
            margin-top: auto;
        }

        .footer-info {
            font-size: 14px;
            margin-bottom: 5px;
        }

        @media (max-width: 414px) {
            header {
                font-size: 20px;
            }

            .container {
                padding: 15px;
            }

            button {
                font-size: 0.9em;
            }

            .forgot-password {
                font-size: 14px;
            }

            footer {
                padding: 15px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<header>
    CantineConnect
</header>

<div class="container">
    <h2>Connexion</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
    <div class="forgot-password">
        <a href="forget_password.php">Mot de passe oublié ?</a>
    </div>
    <p>Pas encore inscrit ? <a href="register.php">Inscrivez-vous ici</a></p>
</div>

<footer>
    <div class="footer-info">Contacts du lycée : 01 23 45 67 89 - contact@lycee.com</div>
    <div>ByWeb - Tous droits réservés © 2024</div>
</footer>

</body>
</html>
