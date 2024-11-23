<?php
session_start();
require 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    echo "Utilisateur non connecté.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT jobs FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erreur dans la préparation de la requête SQL : " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result || $result['jobs'] !== 'admin') {
    echo "Accès refusé. Vous n'êtes pas administrateur.";
    header("Location: login.php");
    exit();
}

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sql_users = "SELECT id, username FROM users WHERE username LIKE ? ORDER BY username";
$stmt_users = $conn->prepare($sql_users);

if (!$stmt_users) {
    die("Erreur dans la préparation de la requête SQL : " . $conn->error);
}

$search_param = "%" . $search . "%";
$stmt_users->bind_param("s", $search_param);
$stmt_users->execute();
$users_result = $stmt_users->get_result();

function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

if (isset($_POST['reset_password']) && isset($_POST['user_id'])) {
    $new_password = generateRandomPassword();
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $user_id_to_update = $_POST['user_id'];

    $sql_update_password = "UPDATE users SET mot_de_passe = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update_password);
    $stmt_update->bind_param("si", $hashed_password, $user_id_to_update);
    $stmt_update->execute();

    $message = "Le mot de passe de l'utilisateur a été réinitialisé. Nouveau mot de passe : " . $new_password;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Administration</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
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
            max-width: 1000px;
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
        }
        input[type="text"] {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            width: 100%;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        button {
            padding: 8px 12px;
            background-color: #FF914D;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #FF5722;
        }
        .message {
            color: green;
            background-color: #e8f7e8;
            border: 1px solid #a6e5a6;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .password-box {
            border: 1px solid red;
            padding: 20px;
            margin-top: 20px;
            background-color: #f9dcdc;
            color: red;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Tableau de bord - Administration</h1>
    </header>

    <div class="container">
        <h2>Gestion des utilisateurs</h2>
        
        <?php if (isset($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        
        <form method="GET" class="search-form">
            <input type="text" id="searchInput" name="search" placeholder="Rechercher par nom d'utilisateur" value="<?= htmlspecialchars($search) ?>" />
            <button type="submit">Rechercher</button>
        </form>

        <table id="usersTable">
            <thead>
                <tr>
                    <th>Nom d'utilisateur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>" />
                                <button type="submit" name="reset_password">Réinitialiser le mot de passe</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var searchValue = this.value;

            var url = new URL(window.location);
            url.searchParams.set('search', searchValue);

            window.history.pushState({}, '', url);
            location.reload();
        });
    </script>
</body>
</html>
