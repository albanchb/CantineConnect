<?php
session_start();
require 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: inscription.php"); 
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
} else {
    header("Location: inscription.php");
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identifiant - CantineConnect</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap">
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

        .username-box {
            font-size: 18px;
            font-weight: bold;
            color: #FF5722;
            margin: 20px 0;
            padding: 10px;
            border: 2px dashed #FF914D;
            border-radius: 5px;
            background-color: #fff8f4;
            text-align: center;
        }

        p {
            font-size: 16px;
            color: #666;
            text-align: center;
            margin-bottom: 20px;
        }

        button {
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: linear-gradient(90deg, #FF914D 0%, #FF5722 100%);
            color: white;
            font-size: 1em;
            cursor: pointer;
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            transition: background-color 0.3s;
        }

        button:hover {
            background: linear-gradient(90deg, #FF5722 0%, #FF914D 100%);
        }

        footer {
            background: linear-gradient(90deg, #FF914D 0%, #FF5722 100%);
            padding: 20px;
            color: white;
            text-align: center;
            font-size: 14px;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            header {
                font-size: 20px;
            }

            .container {
                width: 95%;
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
        }
    </style>
</head>
<body>
    <header>CantineConnect</header>

    <div class="container">
        <h2>Votre identifiant unique</h2>
        <div class="username-box"><?php echo htmlspecialchars($username); ?></div>
        <p>Merci de l'enregistrer sur votre téléphone ou de le noter.<br>Vous devez le conserver.</p>
        <button onclick="location.href='reservation.php'">Réserver mon repas</button>
    </div>

    <footer>
        &copy; 2024 CantineConnect. Tous droits réservés.
    </footer>
</body>
</html>
